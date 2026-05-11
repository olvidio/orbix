<?php

namespace src\permisos\domain;

/**
 * Definición de máscaras para permisos de menú en contexto DL (formulario y listados).
 * Extraído de {@see PermDl::omplir()} para reutilizar sin cargar {@see XPermisos} en el frontend.
 * Para visibilidad/edición de entradas de menú (valores distintos en admin_*) ver {@see \src\menus\domain\PermisoMenuBits}.
 */
final class MenuDlPermissionBits
{
    /**
     * Mismo orden y valores que históricamente en PermDl::omplir().
     *
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
            'admin_sf' => 16776183,
            'admin_sv' => -1,
        ];
    }

    /**
     * Equivalente a {@see XPermisos::lista_txt()} con el mapa DL (una etiqueta si coincide el valor exacto).
     */
    public static function listaTxt(int $bin): string
    {
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = '';
        $i = 0;
        foreach (self::map() as $nom => $num) {
            if ($bin === $num) {
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
     * Equivalente a {@see XPermisos::lista_txt2()} con el mapa DL (subconjuntos por bit).
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
}
