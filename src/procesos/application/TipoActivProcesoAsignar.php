<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Caso de uso: asigna id_tipo_proceso al tipo de actividad (propio / no-propio).
 */
class TipoActivProcesoAsignar
{
    public function __construct(
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_tipo_activ = FuncTablasSupport::inputInt($input, 'id_tipo_activ');
        $Qpropio = FuncTablasSupport::inputString($input, 'propio');
        $Qid_tipo_proceso = FuncTablasSupport::inputInt($input, 'id_tipo_proceso');

        $oTipoDeActividad = $this->tipoDeActividadRepository->findById($Qid_tipo_activ);
        if ($oTipoDeActividad === null) {
            return _('tipo de actividad no encontrado');
        }
        if (FuncTablasSupport::isTrue($Qpropio)) {
            $oTipoDeActividad->setId_tipo_proceso($Qid_tipo_proceso, ConfigGlobal::mi_sfsv());
        } else {
            $oTipoDeActividad->setId_tipo_proceso_ex($Qid_tipo_proceso, ConfigGlobal::mi_sfsv());
        }
        if ($this->tipoDeActividadRepository->Guardar($oTipoDeActividad) === false) {
            return _("hay un error, no se ha guardado el proceso");
        }

        return '';
    }
}
