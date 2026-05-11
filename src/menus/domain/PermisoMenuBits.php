<?php

namespace src\menus\domain;

/**
 * Máscaras de permiso para visibilidad de menús y edición en pantallas de menús.
 * Difiere de {@see \src\permisos\domain\MenuDlPermissionBits} en {@code admin_sf} / {@code admin_sv}.
 *
 * @see PermisoMenu::omplir() histórico
 */
final class PermisoMenuBits
{
    /**
     * @return array<string, int>
     */
    public static function map(): array
    {
        return [
            'adl' => 1,
            'pr' => 1,
            'agd' => 1 << 1,
            'aop' => 1 << 2,
            'des' => 1 << 3,
            'est' => 1 << 4,
            'scdl' => 1 << 5,
            'scr' => 1 << 5,
            'sg' => 1 << 6,
            'sm' => 1 << 7,
            'soi' => 1 << 8,
            'sr' => 1 << 9,
            'vcsd' => 1 << 10,
            'vcsr' => 1 << 10,
            'dtor' => 1 << 11,
            'ocs' => 1 << 12,
            'sddl' => 1 << 13,
            'nax' => 1 << 14,
            'calendario' => 1 << 15,
            'ctr' => 1 << 16,
            'jefeZona' => 1 << 17,
            'sacd' => 1 << 18,
            'persona' => 1 << 19,
            'casa' => 1 << 20,
            'admin_sf' => 1 << 21,
            'admin_sv' => 1 << 25,
        ];
    }

    /**
     * Equivalente a {@see \src\permisos\domain\XPermisos::lista_txt2()} con el mapa de menú.
     */
    public static function listaTxt2(int $bin): string
    {
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = '';
        $i = 0;
        foreach (self::map() as $nom => $num) {
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
     * OR de valores numéricos enviados desde checkboxes (mismo criterio que {@see \src\permisos\domain\XPermisos::permsum_bit()}).
     *
     * @param list<int|string> $selectedValues
     */
    public static function combineSelectedBits(array $selectedValues): int
    {
        $r = 0;
        foreach ($selectedValues as $val) {
            $r |= (int)$val;
        }

        return $r;
    }

    /**
     * @return array<int, string> como {@see \src\permisos\domain\XPermisos::lista_array()} sobre el mapa de menú.
     */
    public static function valueToLabel(): array
    {
        $txt = [];
        foreach (self::map() as $nom => $num) {
            $txt[$num] = $nom;
        }

        return $txt;
    }
}
