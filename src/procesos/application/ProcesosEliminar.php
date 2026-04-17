<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: elimina una tarea_proceso por su id_item.
 */
class ProcesosEliminar
{
    public function execute(array $input): string
    {
        $Qid_item = (int)($input['id_item'] ?? 0);
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $oTareaProceso = $TareaProcesoRepository->findById($Qid_item);
        if ($TareaProcesoRepository->Eliminar($oTareaProceso) === false) {
            return _("hay un error, no se ha eliminado") . "\n" . $TareaProcesoRepository->getErrorTxt();
        }

        return '';
    }
}
