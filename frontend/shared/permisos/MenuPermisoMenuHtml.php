<?php

namespace frontend\shared\permisos;

/**
 * HTML para el mapa de permisos de menú (checkboxes y resumen), sin {@see \src\menus\domain\PermisoMenu}.
 * El mapa debe coincidir con {@see \src\menus\domain\PermisoMenuBits::map()} (p. ej. campo `perm_menu_bit_map` de menus_get_page_data).
 */
final class MenuPermisoMenuHtml
{
    /**
     * @param array<string, int> $bitMap
     */
    public static function listaTxt2(int $bin, array $bitMap): string
    {
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = '';
        $i = 0;
        foreach ($bitMap as $nom => $num) {
            if (($bin & $num) === $num) {
                $i++;
                if ($i > 1) {
                    $txt .= ', ';
                }
                $txt .= $nom;
            }
        }

        return $txt;
    }

    /**
     * @param array<string, int> $bitMap
     */
    public static function cuadrosCheck(string $nomcamp, int $bin, array $bitMap): string
    {
        $camp = $nomcamp . '[]';
        if (empty($bin)) {
            $bin = 0;
        }
        $admin = null;
        foreach ($bitMap as $nom => $num) {
            if ($bin === $num) {
                $admin = $num;
                break;
            }
        }
        $txt = '';
        foreach ($bitMap as $nom => $num) {
            if ($admin !== null) {
                $chk = ($admin == $num) ? 'checked' : '';
            } else {
                $chk = ($bin & $num) ? 'checked' : '';
            }
            $txt .= "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"$num\" $chk>$nom";
        }

        return $txt;
    }
}
