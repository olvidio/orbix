<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;

/**
 * Crea un nuevo tipo de actividad. Portado del case `nuevo` del dispatcher
 * legacy. Devuelve cadena vacia si todo va bien o un texto de error/aviso.
 */
class TipoActivNuevo
{
    public function execute(array $input = []): string
    {
        $Qsfsv = (string)($input['isfsv_val'] ?? '');
        $Qasistentes = (string)($input['iasistentes_val'] ?? '');
        $Qactividad = (string)($input['iactividad_val'] ?? '');
        $Qid_nom_tipo_activ = (string)($input['id_nom_tipo_activ'] ?? '');
        $Qnom_tipo_activ = (string)($input['nom_tipo_activ'] ?? '');

        $id_tipo_activ = "$Qsfsv$Qasistentes$Qactividad$Qid_nom_tipo_activ";

        $mensajes = '';
        if (strlen($id_tipo_activ) !== 6) {
            $mensajes .= _("Id incorrecto");
        }

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $oTipoDeActividad = new TipoDeActividad();
        $oTipoDeActividad->setId_tipo_activ($id_tipo_activ);
        $oTipoDeActividad->setNombre($Qnom_tipo_activ);
        if ($TipoDeActividadRepository->Guardar($oTipoDeActividad) === false) {
            $mensajes .= _("hay un error, no se ha guardado");
        }
        if (ConfigGlobal::is_app_installed('procesos')) {
            $mensajes .= _("IMPORTANTE: Debe añadir un proceso para el nuevo tipo de actividad");
        }

        return $mensajes;
    }
}
