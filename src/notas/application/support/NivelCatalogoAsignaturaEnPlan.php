<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\PlanEstudiosDePersona;

/**
 * Hueco curricular (`id_nivel` del catálogo) de una asignatura obligatoria
 * según el plan de estudios del alumno (1997 vs 2026).
 */
final class NivelCatalogoAsignaturaEnPlan
{
    private const ID_ASIG_OPCIONAL_UMBRAL = 3000;

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }

    public function esObligatoria(int $idAsignatura): bool
    {
        return $idAsignatura > 0 && $idAsignatura <= self::ID_ASIG_OPCIONAL_UMBRAL;
    }

    public function resolve(int $idNom, int $idAsignatura): int
    {
        if ($idAsignatura < 1) {
            throw new \RuntimeException(_('Asignatura no válida.'));
        }

        $plan = $this->planEstudiosDePersona->resolve($idNom);
        $asignatura = $this->asignaturaRepository->findById($idAsignatura, $plan);
        if ($asignatura === null || !$asignatura->isActive()) {
            throw new \RuntimeException(sprintf(
                _('No se encuentra la asignatura %s en el plan de estudios del alumno.'),
                (string) $idAsignatura,
            ));
        }

        return $asignatura->getId_nivel();
    }
}
