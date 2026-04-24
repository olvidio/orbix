<?php

namespace src\ubis\application\services;

use src\shared\config\ConfigGlobal;

/**
 * Reglas de permisos comunes para operar sobre un ubi (centro/casa) según su objeto.
 *
 * Los objetos con sufijo "Dl" sólo son modificables si el ubi pertenece a la
 * delegación del usuario. Los objetos con sufijo "Ex" son modificables por
 * cualquiera con permiso 'scdl'.
 */
final class UbiPermisos
{
    public static function puedeModificar(string $objPau, ?object $oUbi = null): bool
    {
        if (!self::tienePermisoScdl()) {
            return false;
        }
        if (str_contains($objPau, 'Dl')) {
            return $oUbi !== null && $oUbi->getDl() === ConfigGlobal::mi_delef();
        }
        if (str_contains($objPau, 'Ex')) {
            return true;
        }
        return false;
    }

    private static function tienePermisoScdl(): bool
    {
        if (!isset($_SESSION['oPerm'])) {
            return false;
        }
        return (bool)$_SESSION['oPerm']->have_perm_oficina('scdl');
    }
}
