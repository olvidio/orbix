<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: clona las tareas de un proceso de referencia al proceso
 * indicado (borrando las existentes previamente). Devuelve '' si ha ido
 * bien o un mensaje de error. El frontend se encarga de recargar la vista
 * del proceso tras el clonado.
 */
class ProcesosClonar
{
    public function execute(array $input): string
    {
        $Qid_tipo_proceso = (int)($input['id_tipo_proceso'] ?? 0);
        $Qid_tipo_proceso_ref = (int)($input['id_tipo_proceso_ref'] ?? 0);

        if ($Qid_tipo_proceso <= 0 || $Qid_tipo_proceso_ref <= 0) {
            return _("no se ha indicado el proceso a clonar");
        }

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

        return '';
    }
}
