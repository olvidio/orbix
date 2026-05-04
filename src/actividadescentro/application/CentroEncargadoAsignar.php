<?php

namespace src\actividadescentro\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;

/**
 * Asigna un `CentroEncargado` nuevo a una actividad.
 *
 * Calcula `num_orden = max(num_orden) + 1` para que el nuevo centro quede
 * al final del listado. El campo `encargo` queda a 'organizador' por defecto.
 *
 * Sucesor de la rama `asignar` del dispatcher legacy `activ_ctr_ajax.php`.
 */
final class CentroEncargadoAsignar
{
    public static function execute(array $input): string
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        if ($id_activ <= 0 || $id_ubi <= 0) {
            return _("faltan parametros id_activ / id_ubi");
        }

        $repo = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);

        // Calcular num_orden = max(num_orden) + 1.
        $cCentros = $repo->getCentrosEncargados([
            'id_activ' => $id_activ,
            '_ordre' => 'num_orden DESC',
        ]);
        $num_orden = (is_array($cCentros) && count($cCentros) >= 1)
            ? ((int)$cCentros[0]->getNum_orden() + 1)
            : 1; // mejor nop poner 0. Que el primero sea 1

        $oCentroEncargado = new CentroEncargado();
        $oCentroEncargado->setId_activ($id_activ);
        $oCentroEncargado->setId_ubi($id_ubi);
        $oCentroEncargado->setNum_orden($num_orden);
        $oCentroEncargado->setEncargo('organizador');

        if ($repo->Guardar($oCentroEncargado) === false) {
            return _("hay un error, no se ha guardado el centro encargado");
        }
        return '';
    }
}
