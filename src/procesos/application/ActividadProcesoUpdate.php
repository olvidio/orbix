<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use function core\is_true;

/**
 * Caso de uso: guarda el estado (completado/observaciones) de una tarea
 * concreta (id_item) del proceso de una actividad.
 */
class ActividadProcesoUpdate
{
    public function execute(array $input): string
    {
        $Qid_item = (int)($input['id_item'] ?? 0);
        $Qcompletado = (string)($input['completado'] ?? '');
        $Qobserv = (string)($input['observ'] ?? '');

        $ProcesoActividadService = $GLOBALS['container']->get(ProcesoActividadService::class);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $oFicha = $ActividadProcesoTareaRepository->findById($Qid_item);
        $oFicha->setCompletado(is_true($Qcompletado));
        $oFicha->setObserv($Qobserv);
        if ($ProcesoActividadService->guardar($oFicha) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $ActividadProcesoTareaRepository->getErrorTxt();
        }

        return '';
    }
}
