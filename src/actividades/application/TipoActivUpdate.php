<?php

namespace src\actividades\application;

use frontend\actividades\helpers\TipoActivMetadataLoader;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;

/**
 * Actualiza el nombre de un tipo de actividad. Portado del case `update` del
 * dispatcher legacy.
 */
class TipoActivUpdate
{
    public function execute(array $input = []): string
    {
        $Qid_tipo_activ = (int)($input['id_tipo_activ'] ?? 0);
        $Qnom_tipo_activ = (string)($input['nom_tipo_activ'] ?? '');

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $oTipoDeActividad = $TipoDeActividadRepository->findById($Qid_tipo_activ);
        $oTipoDeActividad->setNombre($Qnom_tipo_activ);
        if ($TipoDeActividadRepository->Guardar($oTipoDeActividad) === false) {
            return _("hay un error, no se ha guardado");
        }

        // El nombre cacheado en sesión por TipoActivMetadataLoader queda
        // obsoleto al editar el tipo: forzar refetch en la próxima lectura.
        TipoActivMetadataLoader::forget();

        return '';
    }
}
