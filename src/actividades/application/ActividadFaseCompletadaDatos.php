<?php

namespace src\actividades\application;

use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

/**
 * Comprueba si una fase está completada para la actividad (equivale a
 * {@see ActividadProcesoTareaRepositoryInterface::faseCompletada}).
 * Endpoint HTTP para consultas unitarias desde capas sin contenedor.
 */
final class ActividadFaseCompletadaDatos
{
    /**
     * @return array{completada: bool}
     */
    public function ejecutar(int $idActiv, int $idFase): array
    {
        if ($idActiv <= 0 || $idFase <= 0) {
            return ['completada' => false];
        }

        $repo = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);

        return ['completada' => $repo->faseCompletada($idActiv, $idFase)];
    }
}
