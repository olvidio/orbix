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
    public function __construct(
        private TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input = []): string
    {
        $Qid_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_tipo_activ');

        $TipoDeActividadRepository = $this->tipoDeActividadRepository;
        $oTipoDeActividad = $TipoDeActividadRepository->findById($Qid_tipo_activ);
        if ($oTipoDeActividad === null) {
            return _('tipo de actividad no encontrado');
        }
        if ($TipoDeActividadRepository->Eliminar($oTipoDeActividad) === false) {
            return _("hay un error, no se ha eliminado");
        }

        // El listado cacheado en sesión por TipoActivMetadataLoader queda
        // obsoleto al eliminar un tipo: forzar refetch en la próxima lectura.
        TipoActivMetadataLoader::forget();

        return '';
    }
}
