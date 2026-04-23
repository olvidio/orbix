<?php

namespace src\actividadplazas\application;

use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;

/**
 * Guarda las peticiones de una persona+tipo. Borra todas las
 * anteriores y crea las nuevas en el orden recibido.
 *
 * Sucesor de la rama `update` del dispatcher legacy
 * `apps/actividadplazas/controller/peticiones_activ_ajax.php`.
 */
final class PeticionesGuardar
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
            $repo->Eliminar($oPlazaPeticion);
        }

        $a_actividades = $input['actividades'] ?? [];
        if (!is_array($a_actividades)) {
            $a_actividades = [];
        }
        $i = 0;
        foreach ($a_actividades as $id_activ) {
            $id_activ = (int)$id_activ;
            if ($id_activ === 0) {
                continue;
            }
            $i++;
            $oPlazaPeticion = $repo->findById($id_nom, $id_activ);
            if ($oPlazaPeticion === null) {
                $oPlazaPeticion = new PlazaPeticion();
                $oPlazaPeticion->setId_nom($id_nom);
                $oPlazaPeticion->setId_activ($id_activ);
            }
            $oPlazaPeticion->setOrden($i);
            $oPlazaPeticion->setTipo($sactividad);
            if ($repo->Guardar($oPlazaPeticion) === false) {
                return (string)_("hay un error, no se han guardado todas las peticiones");
            }
        }
        return '';
    }
}
