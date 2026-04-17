<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: clona las tareas de un proceso de referencia al proceso
 * indicado (borrando las existentes previamente). Devuelve la vista
 * del proceso resultante (equivalente a `get`) para coincidir con el
 * comportamiento del dispatcher legacy.
 */
class ProcesosClonar
{
    public function execute(array $input): string
    {
        $Qid_tipo_proceso = (int)($input['id_tipo_proceso'] ?? 0);
        $Qid_tipo_proceso_ref = (int)($input['id_tipo_proceso_ref'] ?? 0);

        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso]);
        foreach ($cTareasProceso as $oTareaProceso) {
            $TareaProcesoRepository->Eliminar($oTareaProceso);
        }
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso_ref]);
        foreach ($cTareasProceso as $oTareaProceso) {
            $oTareaProceso->setId_tipo_proceso($Qid_tipo_proceso);
            $newId_item = $TareaProcesoRepository->getNewId();
            $oTareaProceso->setId_item($newId_item);
            $TareaProcesoRepository->Guardar($oTareaProceso);
        }

        return (new ProcesosGet())->execute($input);
    }
}
