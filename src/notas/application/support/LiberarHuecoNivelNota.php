<?php

declare(strict_types=1);

namespace src\notas\application\support;

use RuntimeException;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\TipoActa;

/**
 * La PK de `e_notas` es `(id_nom, id_nivel, tipo_acta)`. Tras el plan 2026
 * el mismo `id_nivel` puede ser otra asignatura (Latín III 1997 vs Latín IV 2026).
 * Si el hueco está ocupado por otra asignatura, se reubica al `id_nivel` del
 * catálogo del plan actual (igual que la migración 152200) para poder insertar.
 */
final class LiberarHuecoNivelNota
{
    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }

    public function asegurarLibre(
        PersonaNotaRepositoryInterface $repo,
        int $idNom,
        int $idNivel,
        int $idAsignaturaNueva,
    ): void {
        $ocupantes = $repo->getPersonaNotas([
            'id_nom' => $idNom,
            'id_nivel' => $idNivel,
        ]);
        if ($ocupantes === []) {
            return;
        }

        $plan = $this->planEstudiosDePersona->resolve($idNom);
        foreach ($ocupantes as $ocupante) {
            if ($ocupante->getId_asignatura() === $idAsignaturaNueva) {
                continue;
            }
            $this->reubicarOcupante($repo, $ocupante, $idNivel, $idAsignaturaNueva, $plan);
        }
    }

    private function reubicarOcupante(
        PersonaNotaRepositoryInterface $repo,
        PersonaNota $ocupante,
        int $idNivelDeseado,
        int $idAsignaturaNueva,
        int $plan,
    ): void {
        $idOcupante = $ocupante->getId_asignatura();
        $asigOcupante = $this->asignaturaRepository->findById($idOcupante, $plan);
        $nombreOcupante = $asigOcupante?->getNombre_corto() ?? (string) $idOcupante;
        $asigNueva = $this->asignaturaRepository->findById($idAsignaturaNueva, $plan);
        $nombreNueva = $asigNueva?->getNombre_corto() ?? (string) $idAsignaturaNueva;

        if ($asigOcupante === null || !$asigOcupante->isActive()) {
            throw new RuntimeException(sprintf(
                _("No se puede grabar %s: el hueco %s lo ocupa %s (id_asignatura %s, acta %s), que no está en este plan y no se puede reubicar."),
                $nombreNueva,
                (string) $idNivelDeseado,
                $nombreOcupante,
                (string) $idOcupante,
                (string) ($ocupante->getActa() ?? ''),
            ));
        }

        $nivelCatalogo = $asigOcupante->getId_nivel();
        if ($nivelCatalogo === $idNivelDeseado) {
            throw new RuntimeException(sprintf(
                _("No se puede grabar %s: el hueco %s lo ocupa %s (acta %s)."),
                $nombreNueva,
                (string) $idNivelDeseado,
                $nombreOcupante,
                (string) ($ocupante->getActa() ?? ''),
            ));
        }

        $yaEnDestino = $repo->getPersonaNotas([
            'id_nom' => $ocupante->getId_nom(),
            'id_nivel' => $nivelCatalogo,
        ]);
        if ($yaEnDestino !== []) {
            $bloqueante = $yaEnDestino[0];
            $asigBloqueante = $this->asignaturaRepository->findById($bloqueante->getId_asignatura(), $plan);
            $nombreBloqueante = $asigBloqueante?->getNombre_corto() ?? (string) $bloqueante->getId_asignatura();
            throw new RuntimeException(sprintf(
                _("No se puede grabar %s: hay que mover %s del hueco %s al %s, pero ese hueco lo ocupa %s (acta %s)."),
                $nombreNueva,
                $nombreOcupante,
                (string) $idNivelDeseado,
                (string) $nivelCatalogo,
                $nombreBloqueante,
                (string) ($bloqueante->getActa() ?? ''),
            ));
        }

        $ok = $repo->actualizarIdNivel(
            $ocupante->getId_nom(),
            $ocupante->getId_nivel(),
            $ocupante->getTipo_acta() ?? TipoActa::FORMATO_ACTA,
            $nivelCatalogo,
        );
        if ($ok !== true) {
            throw new RuntimeException(sprintf(
                _("No se ha podido reubicar %s del hueco %s al %s."),
                $nombreOcupante,
                (string) $idNivelDeseado,
                (string) $nivelCatalogo,
            ));
        }
    }
}
