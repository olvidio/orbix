<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Caso de uso: elimina una tarea_proceso por su id_item.
 */
class ProcesosEliminar
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
        $Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_item');
        if ($Qid_item <= 0) {
            return _("no sé cuál he de borar");
        }
        $oTareaProceso = $this->tareaProcesoRepository->findById($Qid_item);
        if ($oTareaProceso === null) {
            return _("no se encuentra la tarea a borrar");
        }
        if ($this->tareaProcesoRepository->Eliminar($oTareaProceso) === false) {
            return _("hay un error, no se ha eliminado") . "\n" . $this->tareaProcesoRepository->getErrorTxt();
        }

        return '';
    }
}
