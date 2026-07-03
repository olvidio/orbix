<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Caso de uso: (re)genera las tareas del proceso asociado a un id_activ.
 */
class ActividadProcesoGenerar
{
    public function __construct(
        private readonly ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $this->actividadProcesoTareaRepository->generarProceso((string) $Qid_activ, ConfigGlobal::mi_sfsv(), true);

        return '';
    }
}
