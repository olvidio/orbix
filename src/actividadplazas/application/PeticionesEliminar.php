<?php

namespace src\actividadplazas\application;

use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;

/**
 * Elimina todas las peticiones de plaza para un {id_nom, tipo}.
 *
 * Sucesor de la rama `borrar` del dispatcher legacy
 * `apps/actividadplazas/controller/peticiones_activ_ajax.php`.
 */
final class PeticionesEliminar
{
    public static function execute(array $input): string
    {
        $id_nom = (int)($input['id_nom'] ?? 0);
        $sactividad = (string)($input['sactividad'] ?? '');
        if ($id_nom <= 0 || $sactividad === '') {
            return (string)_("faltan parametros id_nom / sactividad");
        }

        $repo = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
        $cPlazasPeticion = $repo->getPlazasPeticion([
            'id_nom' => $id_nom,
            'tipo' => $sactividad,
        ]);
        foreach ($cPlazasPeticion as $oPlazaPeticion) {
            if ($repo->Eliminar($oPlazaPeticion) === false) {
                return (string)_("hay un error, no se ha podido eliminar");
            }
        }
        return '';
    }
}
