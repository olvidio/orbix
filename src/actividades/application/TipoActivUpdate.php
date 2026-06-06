<?php

namespace src\actividades\application;

use frontend\actividades\helpers\TipoActivMetadataLoader;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Actualiza el nombre de un tipo de actividad. Portado del case `update` del
 * dispatcher legacy.
 */
class TipoActivUpdate
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
        $Qid_tipo_activ = input_int($input, 'id_tipo_activ');
        $Qnom_tipo_activ = input_string($input, 'nom_tipo_activ');

        $TipoDeActividadRepository = $this->tipoDeActividadRepository;
        $oTipoDeActividad = $TipoDeActividadRepository->findById($Qid_tipo_activ);
        if ($oTipoDeActividad === null) {
            return _('tipo de actividad no encontrado');
        }
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
