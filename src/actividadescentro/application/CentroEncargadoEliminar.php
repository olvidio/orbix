<?php

namespace src\actividadescentro\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;

/**
 * Elimina un `CentroEncargado` ({id_activ, id_ubi}) del listado de centros
 * encargados de una actividad.
 *
 * Sucesor de la rama `orden` con `num_orden = 'borrar'` del dispatcher
 * legacy `activ_ctr_ajax.php`.
 */
final class CentroEncargadoEliminar
{
    public static function execute(array $input): string
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        if ($id_activ <= 0 || $id_ubi <= 0) {
            return _("no se sabe cual borrar");
        }

        $repo = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $oCentro = $repo->findById($id_activ, $id_ubi);
        if ($oCentro === null) {
            return _("el centro encargado ya no existe");
        }
        if ($repo->Eliminar($oCentro) === false) {
            return _("hay un error, no se ha eliminado el centro");
        }
        return '';
    }
}
