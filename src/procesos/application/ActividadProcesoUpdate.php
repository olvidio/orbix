<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Caso de uso: guarda el estado (completado/observaciones) de una tarea del proceso.
 */
class ActividadProcesoUpdate
{
    public function __construct(
        private readonly ProcesoActividadService $procesoActividadService,
        private readonly ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_item = FuncTablasSupport::inputInt($input, 'id_item');
        $Qcompletado = FuncTablasSupport::inputString($input, 'completado');
        $Qobserv = FuncTablasSupport::inputString($input, 'observ');

        $oFicha = $this->actividadProcesoTareaRepository->findById($Qid_item);
        if ($oFicha === null) {
            return _('no se encuentra la tarea del proceso');
        }
        $oFicha->setCompletado(FuncTablasSupport::isTrue($Qcompletado));
        $oFicha->setObserv($Qobserv);
        if ($this->procesoActividadService->guardar($oFicha) === false) {
            $err = $this->procesoActividadService->getErrorTxt();
            if ($err !== '') {
                return $err;
            }

            return _("hay un error, no se ha guardado") . "\n" . $this->actividadProcesoTareaRepository->getErrorTxt();
        }

        return '';
    }
}
