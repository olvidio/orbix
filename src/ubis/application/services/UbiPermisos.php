<?php

namespace src\ubis\application\services;

use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermDl;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;

/**
 * Reglas de permisos comunes para operar sobre un ubi (centro/casa) según su objeto.
 *
 * Centro/Casa (y sus Direccion*): sólo lectura.
 * CentroDl/CasaDl: modificables si el ubi pertenece a la delegación del usuario.
 * CentroEx/CasaEx: modificables con permiso 'scdl'.
 */
final class UbiPermisos
{
    public static function normalizeObjPau(string $obj): string
    {
        return match ($obj) {
            'DireccionCentro' => 'Centro',
            'DireccionCentroDl' => 'CentroDl',
            'DireccionCentroEx' => 'CentroEx',
            'DireccionCdc' => 'Casa',
            'DireccionCdcDl' => 'CasaDl',
            'DireccionCdcEx' => 'CasaEx',
            default => $obj,
        };
    }

    public static function puedeModificar(string $obj, ?object $oUbi = null): bool
    {
        $dl = null;
        if ($oUbi !== null && method_exists($oUbi, 'getDl')) {
            $dl = $oUbi->getDl();
        }

        return self::puedeModificarPorObjeto($obj, is_string($dl) ? $dl : null);
    }

    public static function puedeModificarPorObjeto(string $obj, ?string $dlUbi = null): bool
    {
        if (!self::tienePermisoScdl()) {
            return false;
        }

        return match (self::normalizeObjPau($obj)) {
            'CentroDl', 'CasaDl' => self::dlPerteneceAMiDelegacion($dlUbi),
            'CentroEx', 'CasaEx' => true,
            'Centro', 'Casa' => false,
            default => false,
        };
    }

    public static function dlPerteneceAMiDelegacion(?string $dlUbi): bool
    {
        if ($dlUbi === null || $dlUbi === '') {
            return false;
        }

        $candidatos = array_unique(array_filter([
            ConfigGlobal::mi_delef(),
            ConfigGlobal::mi_dele(),
            rtrim(ConfigGlobal::mi_delef(), 'f'),
        ], static fn (string $v): bool => $v !== ''));

        return in_array($dlUbi, $candidatos, true);
    }

    private static function tienePermisoScdl(): bool
    {
        $oPerm = $_SESSION['oPerm'] ?? null;
        if (!$oPerm instanceof XPermisos && !empty($_SESSION['iPermMenus'])) {
            $oPerm = new PermDl();
            $oPerm->setAccion((int) $_SESSION['iPermMenus']);
        }
        if ($oPerm instanceof XPermisos) {
            return (bool) $oPerm->have_perm_oficina('scdl');
        }

        $iPerm = $_SESSION['iPermMenus'] ?? 0;
        if (!is_numeric($iPerm)) {
            return false;
        }
        $scdlBit = MenuDlPermissionBits::map()['scdl'] ?? 0;

        return ((int) $iPerm & (int) $scdlBit) !== 0;
    }
}
