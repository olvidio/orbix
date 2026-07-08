<?php

namespace src\cambios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;

/**
 * Resuelve una actividad referenciada desde un cambio de aviso.
 *
 * Las importadas de otra dl pueden estar en `a_actividades_ex` (resto) antes
 * de aparecer en `public.a_actividades_all`.
 */
final class ActividadParaAvisoLookup
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ActividadExRepositoryInterface $actividadExRepository,
    ) {
    }

    public function find(int $id_activ): ?ActividadAll
    {
        if ($id_activ <= 0) {
            return null;
        }

        $oActividad = $this->actividadAllRepository->findById($id_activ);
        if ($oActividad !== null) {
            return $oActividad;
        }

        return $this->actividadExRepository->findById($id_activ);
    }
}
