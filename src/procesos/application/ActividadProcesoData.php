<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;

/**
 * Caso de uso: datos para la pantalla `actividad_proceso` (vista de
 * las fases del proceso de una actividad concreta).
 */
class ActividadProcesoData
{
    public static function execute(int $id_activ): array
    {
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($id_activ);

        return [
            'id_activ' => $id_activ,
            'nom_activ' => $oActividad === null ? '' : $oActividad->getNom_activ(),
        ];
    }
}
