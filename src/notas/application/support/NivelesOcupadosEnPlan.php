<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;

/**
 * Huecos curriculares (`id_nivel` del catálogo del plan) ya cubiertos por notas.
 *
 * No se puede usar el `id_nivel` almacenado en `e_notas`: tras el remap 2026
 * el mismo número de nivel significa asignaturas distintas (p. ej. Latín III
 * 1997 y Latín IV 2026 comparten el 2212). Igual que {@see \src\notas\application\Tesera}:
 * obligatorias → nivel del catálogo del plan; opcionales concretas
 * (`id_asignatura` entre 3000 y 9000) → nivel de la nota (slot genérico).
 * Las marcas de fin de ciclo (9998/9999) no ocupan hueco de opcional.
 */
final class NivelesOcupadosEnPlan
{

    /**
     * @param iterable<PersonaNota|PersonaNotaOtraRegionStgr> $notas
     * @return array<int, true>
     */
    public static function ocupados(
        iterable $notas,
        int $plan,
        AsignaturaRepositoryInterface $asignaturaRepository,
    ): array {
        $ocupados = [];
        foreach ($notas as $nota) {
            $nivel = self::nivelDelPlan($nota, $plan, $asignaturaRepository);
            if ($nivel === null) {
                continue;
            }
            $ocupados[$nivel] = true;
        }

        return $ocupados;
    }

    public static function nivelDelPlan(
        PersonaNota|PersonaNotaOtraRegionStgr $nota,
        int $plan,
        AsignaturaRepositoryInterface $asignaturaRepository,
    ): ?int {
        $idAsignatura = $nota->getId_asignatura();
        if (HuecoOpcionalDePlan::esMarcadorFinCiclo($idAsignatura)) {
            return null;
        }
        if (HuecoOpcionalDePlan::esOpcionalConcreta($idAsignatura)) {
            $nivelNota = $nota->getId_nivel();

            return $nivelNota > 0 ? $nivelNota : null;
        }

        $asignatura = $asignaturaRepository->findById($idAsignatura, $plan);
        if ($asignatura === null || !$asignatura->isActive()) {
            return null;
        }

        return $asignatura->getId_nivel();
    }
}
