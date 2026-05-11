<?php

namespace src\inventario\application;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;

/**
 * Borrado de un equipaje (antes solo en `equipajes_eliminar.php`).
 */
final class EquipajeEliminar
{
    public static function execute(int $id_equipaje): string
    {
        if ($id_equipaje <= 0) {
            return (string)_('falta id_equipaje');
        }

        $repo = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
        $oEquipaje = $repo->findById($id_equipaje);
        if ($oEquipaje === null) {
            return (string)sprintf(_('No se encuentra el equipaje %d'), $id_equipaje);
        }
        if ($repo->Eliminar($oEquipaje) === false) {
            return (string)_('hay un error, no se ha eliminado') . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
