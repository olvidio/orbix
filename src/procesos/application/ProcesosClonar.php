<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Caso de uso: clona las tareas de un proceso de referencia al proceso indicado.
 */
class ProcesosClonar
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
        $Qid_tipo_proceso = FuncTablasSupport::inputInt($input, 'id_tipo_proceso');
        $Qid_tipo_proceso_ref = FuncTablasSupport::inputInt($input, 'id_tipo_proceso_ref');

        if ($Qid_tipo_proceso <= 0 || $Qid_tipo_proceso_ref <= 0) {
            return _("no se ha indicado el proceso a clonar");
        }

        $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso]);
        foreach ($cTareasProceso as $oTareaProceso) {
            $this->tareaProcesoRepository->Eliminar($oTareaProceso);
        }
        $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso_ref]);
        foreach ($cTareasProceso as $oTareaProceso) {
            $oTareaProceso->setId_tipo_proceso($Qid_tipo_proceso);
            $newId_item = $this->tareaProcesoRepository->getNewId();
            $oTareaProceso->setId_item($newId_item);
            $this->tareaProcesoRepository->Guardar($oTareaProceso);
        }

        return '';
    }
}
