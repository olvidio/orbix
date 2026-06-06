<?php

namespace src\actividadescentro\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use function src\shared\domain\helpers\input_int;

/**
 * Elimina un `CentroEncargado` ({id_activ, id_ubi}) del listado de centros
 * encargados de una actividad.
 *
 * Sucesor de la rama `orden` con `num_orden = 'borrar'` del dispatcher
 * legacy `activ_ctr_ajax.php`.
 */
final class CentroEncargadoEliminar
{
    public function __construct(
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_activ = input_int($input, 'id_activ');
        $id_ubi = input_int($input, 'id_ubi');
        if ($id_activ <= 0 || $id_ubi <= 0) {
            return _("no se sabe cual borrar");
        }

        $oCentro = $this->centroEncargadoRepository->findById($id_activ, $id_ubi);
        if ($oCentro === null) {
            return _("el centro encargado ya no existe");
        }
        if ($this->centroEncargadoRepository->Eliminar($oCentro) === false) {
            return _("hay un error, no se ha eliminado el centro");
        }
        return '';
    }
}
