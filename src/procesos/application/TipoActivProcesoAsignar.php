<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use function src\shared\domain\helpers\is_true;

/**
 * Caso de uso: asigna un id_tipo_proceso al tipo de actividad indicado,
 * distinguiendo entre proceso propio (dl) o no-propio segun `propio`.
 */
class TipoActivProcesoAsignar
{
    public function execute(array $input): string
    {
        $Qid_tipo_activ = (int)($input['id_tipo_activ'] ?? 0);
        $Qpropio = (string)($input['propio'] ?? '');
        $Qid_tipo_proceso = (int)($input['id_tipo_proceso'] ?? 0);

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $oTipoDeActividad = $TipoDeActividadRepository->findById($Qid_tipo_activ);
        if (is_true($Qpropio)) {
            $oTipoDeActividad->setId_tipo_proceso($Qid_tipo_proceso, ConfigGlobal::mi_sfsv());
        } else {
            $oTipoDeActividad->setId_tipo_proceso_ex($Qid_tipo_proceso, ConfigGlobal::mi_sfsv());
        }
        if ($TipoDeActividadRepository->Guardar($oTipoDeActividad) === false) {
            return _("hay un error, no se ha guardado el proceso");
        }

        return '';
    }
}
