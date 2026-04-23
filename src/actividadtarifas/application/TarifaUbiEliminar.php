<?php

namespace src\actividadtarifas\application;

use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;

/**
 * Mutacion: elimina una `TarifaUbi`.
 *
 * Sucesor de las ramas `borrar` y `tar_ubi_eliminar` del dispatcher
 * legacy `apps/actividadtarifas/controller/tarifa_ajax.php` (ambas
 * ejecutaban la misma accion con nombres distintos).
 */
final class TarifaUbiEliminar
{
    public static function execute(array $input): string
    {
        $id_item = (int)($input['id_item'] ?? 0);
        if ($id_item === 0) {
            return (string)_("no sé cuál he de borrar");
        }

        $repo = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
        $oTarifaUbi = $repo->findById($id_item);
        if ($oTarifaUbi === null) {
            return (string)_("no se encuentra la tarifa");
        }

        if ($repo->Eliminar($oTarifaUbi) === false) {
            return (string)_("hay un error, no se ha borrado")
                . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
