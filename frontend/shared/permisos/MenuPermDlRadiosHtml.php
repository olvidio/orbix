<?php

namespace frontend\shared\permisos;

/**
 * Radios de permiso menú (mapa DL) sin depender de {@see \src\permisos\domain\XPermisos}.
 * El mapa de bits debe coincidir con {@see \src\permisos\domain\MenuDlPermissionBits::map()}
 * (se entrega vía API en perm_menu_info).
 */
final class MenuPermDlRadiosHtml
{
    /**
     * @param array<string, int> $bitMap p. ej. clave `menu_perm_dl_map` de perm_menu_info
     */
    public static function cuadrosRadio(string $nomcamp, int $menu_perm, array $bitMap): string
    {
        $camp = $nomcamp . '[]';
        $txt = '';
        foreach ($bitMap as $nom => $num) {
            $chk = ($menu_perm == $num) ? 'checked' : '';
            $txt .= "   <input type=\"radio\" id=\"$camp\" name=\"$camp\" value=\"$num\" $chk>$nom";
        }

        return $txt;
    }
}
