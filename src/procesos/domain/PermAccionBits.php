<?php

namespace src\procesos\domain;

/**
 * Máscaras de acción de fase (mismo mapa que {@see PermAccion}).
 */
final class PermAccionBits
{
    /**
     * @return array<string, int>
     */
    public static function map(): array
    {
        return [
            'nada' => 0,
            'ocupado' => 1,
            'ver' => 3,
            'modificar' => 7,
            'crear' => 15,
            'borrar' => 31,
        ];
    }

    /**
     * @return array<int, string> valor numérico => etiqueta (como {@see XPermisos::lista_array}).
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
