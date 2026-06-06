<?php

namespace src\actividades\application;

use frontend\actividades\helpers\TipoActivMetadataLoader;
use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;

use function src\shared\domain\helpers\input_string;

/**
 * Crea un nuevo tipo de actividad. Portado del case `nuevo` del dispatcher
 * legacy. Devuelve cadena vacia si todo va bien o un texto de error/aviso.
 */
class TipoActivNuevo
{
    public function __construct(
        private TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input = []): string
    {
        $Qsfsv = input_string($input, 'isfsv_val');
        $Qasistentes = input_string($input, 'iasistentes_val');
        $Qactividad = input_string($input, 'iactividad_val');
        $Qid_nom_tipo_activ = input_string($input, 'id_nom_tipo_activ');
        $Qnom_tipo_activ = input_string($input, 'nom_tipo_activ');

        $id_tipo_activ = "$Qsfsv$Qasistentes$Qactividad$Qid_nom_tipo_activ";

        $mensajes = '';
        if (strlen($id_tipo_activ) !== 6) {
            $mensajes .= _("Id incorrecto");
        }

        $TipoDeActividadRepository = $this->tipoDeActividadRepository;
        $oTipoDeActividad = new TipoDeActividad();
        $oTipoDeActividad->setId_tipo_activ((int) $id_tipo_activ);
        $oTipoDeActividad->setNombre($Qnom_tipo_activ);
        if ($TipoDeActividadRepository->Guardar($oTipoDeActividad) === false) {
            $mensajes .= _("hay un error, no se ha guardado");
        } else {
            // El listado cacheado en sesión por TipoActivMetadataLoader queda
            // obsoleto al añadir un nuevo tipo: forzar refetch en la próxima
            // lectura.
            TipoActivMetadataLoader::forget();
        }
        if (ConfigGlobal::is_app_installed('procesos')) {
            $mensajes .= _("IMPORTANTE: Debe añadir un proceso para el nuevo tipo de actividad");
        }

        return $mensajes;
    }
}
