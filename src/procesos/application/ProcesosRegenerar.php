<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: regenera las tareas del proceso a partir de las fases
 * definidas en `tareas_proceso`, eliminando las sobrantes.
 */
class ProcesosRegenerar
{
    public function execute(array $input): string
    {
        $Qid_tipo_proceso = (int)($input['id_tipo_proceso'] ?? 0);

        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso]);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $id_fase = 0;
        $id_tarea = 0;
        foreach ($cTareasProceso as $oTareaProceso) {
            $id_fase = $oTareaProceso->getId_fase();
            $id_tarea = $oTareaProceso->getId_tarea();
            $ActividadProcesoTareaRepository->addFaseTarea($Qid_tipo_proceso, $id_fase, $id_tarea);
        }
        $ActividadProcesoTareaRepository->borrarFaseTareaInexistente($Qid_tipo_proceso, $id_fase, $id_tarea);

        return '';
    }
}
