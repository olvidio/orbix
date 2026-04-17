<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

/**
 * Caso de uso: (re)genera las tareas del proceso asociado a un id_activ,
 * conservando el estado actual segun el flag `force=true`.
 */
class ActividadProcesoGenerar
{
    public function execute(array $input): string
    {
        $Qid_activ = (int)($input['id_activ'] ?? 0);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $ActividadProcesoTareaRepository->generarProceso($Qid_activ, ConfigGlobal::mi_sfsv(), true);

        return '';
    }
}
