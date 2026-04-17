<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: guarda una tarea_proceso (fase/tarea/responsable/status
 * y fases_previas) del proceso.
 */
class ProcesosUpdate
{
    public function execute(array $input): string
    {
        $Qid_item = (int)($input['id_item'] ?? 0);
        $Qid_tipo_proceso = (int)($input['id_tipo_proceso'] ?? 0);
        $Qstatus = (int)($input['status'] ?? 0);
        $Qid_of_responsable = (int)($input['id_of_responsable'] ?? 0);
        $Qid_fase = (int)($input['id_fase'] ?? 0);
        $Qid_tarea = (int)($input['id_tarea'] ?? 0);
        $Qid_fase_previa = (array)($input['id_fase_previa'] ?? []);
        $Qid_tarea_previa = (array)($input['id_tarea_previa'] ?? []);
        $Qmensaje_requisito = (array)($input['mensaje_requisito'] ?? []);

        $aFases_previas = [];
        $num_fases_previas = count($Qid_fase_previa);
        for ($i = 0; $i < $num_fases_previas; $i++) {
            if (empty($Qid_fase_previa[$i])) {
                continue;
            }
            $aFases_previas[] = [
                'id_fase' => $Qid_fase_previa[$i],
                'id_tarea' => $Qid_tarea_previa[$i] ?? '',
                'mensaje' => $Qmensaje_requisito[$i] ?? '',
            ];
        }
        if (empty($Qid_tarea)) {
            $Qid_tarea = 0;
        }

        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $oTareaProceso = $TareaProcesoRepository->findById($Qid_item);
        $oTareaProceso->setId_tipo_proceso($Qid_tipo_proceso);
        $oTareaProceso->setStatus($Qstatus);
        $oTareaProceso->setId_of_responsable($Qid_of_responsable);
        $oTareaProceso->setId_fase($Qid_fase);
        $oTareaProceso->setId_tarea($Qid_tarea);
        $oTareaProceso->setJson_fases_previas($aFases_previas);
        if ($TareaProcesoRepository->Guardar($oTareaProceso) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $TareaProcesoRepository->getErrorTxt();
        }

        return '';
    }
}
