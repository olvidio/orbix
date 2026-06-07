<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use function src\shared\domain\helpers\input_int;

/**
 * Caso de uso: regenera las tareas del proceso a partir de tareas_proceso.
 */
class ProcesosRegenerar
{
    public function __construct(
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
        private readonly ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_tipo_proceso = input_int($input, 'id_tipo_proceso');

        $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso]);
        $id_fase = 0;
        $id_tarea = 0;
        foreach ($cTareasProceso as $oTareaProceso) {
            $id_fase = $oTareaProceso->getId_fase();
            $id_tarea = $oTareaProceso->getId_tarea();
            $this->actividadProcesoTareaRepository->addFaseTarea($Qid_tipo_proceso, $id_fase, $id_tarea);
        }
        $this->actividadProcesoTareaRepository->borrarFaseTareaInexistente($Qid_tipo_proceso, $id_fase, $id_tarea);

        return '';
    }
}
