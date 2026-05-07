<?php

namespace src\actividades\application;

use frontend\actividades\helpers\TipoActivMetadataLoader;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;

/**
 * Elimina un tipo de actividad. Portado del case `eliminar` del dispatcher
 * legacy.
 */
class TipoActivEliminar
{
    public function execute(array $input = []): string
    {
        $Qid_tipo_activ = (int)($input['id_tipo_activ'] ?? 0);

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $oTipoDeActividad = $TipoDeActividadRepository->findById($Qid_tipo_activ);
        if ($TipoDeActividadRepository->Eliminar($oTipoDeActividad) === false) {
            return _("hay un error, no se ha eliminado");
        }

        // El listado cacheado en sesión por TipoActivMetadataLoader queda
        // obsoleto al eliminar un tipo: forzar refetch en la próxima lectura.
        TipoActivMetadataLoader::forget();

        return '';
    }
}
