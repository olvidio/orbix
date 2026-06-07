<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use function src\shared\domain\helpers\input_int;

/**
 * Caso de uso: guarda una tarea_proceso del proceso.
 */
class ProcesosUpdate
{
    public function __construct(
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_item = input_int($input, 'id_item');
        $Qid_tipo_proceso = input_int($input, 'id_tipo_proceso');
        $Qstatus = input_int($input, 'status');
        $Qid_of_responsable = input_int($input, 'id_of_responsable');
        $Qid_fase = input_int($input, 'id_fase');
        $Qid_tarea = input_int($input, 'id_tarea');
        /** @var list<mixed> $Qid_fase_previa */
        $Qid_fase_previa = (array)($input['id_fase_previa'] ?? []);
        /** @var list<mixed> $Qid_tarea_previa */
        $Qid_tarea_previa = (array)($input['id_tarea_previa'] ?? []);
        /** @var list<mixed> $Qmensaje_requisito */
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
        if ($Qid_tarea === 0) {
            $Qid_tarea = 0;
        }

        $oTareaProceso = $this->tareaProcesoRepository->findById($Qid_item);
        if ($oTareaProceso === null) {
            return _('no se encuentra la tarea del proceso');
        }
        $oTareaProceso->setId_tipo_proceso($Qid_tipo_proceso);
        $oTareaProceso->setStatus($Qstatus);
        $oTareaProceso->setId_of_responsable($Qid_of_responsable);
        $oTareaProceso->setId_fase($Qid_fase);
        $oTareaProceso->setId_tarea($Qid_tarea);
        $oTareaProceso->setJson_fases_previas($aFases_previas);
        if ($this->tareaProcesoRepository->Guardar($oTareaProceso) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $this->tareaProcesoRepository->getErrorTxt();
        }

        return '';
    }
}
