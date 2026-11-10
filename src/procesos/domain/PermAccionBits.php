<?php

namespace src\procesos\domain;

/**
 * Máscaras de acción de fase (mismo mapa que {@see PermAccion}).
 */
final class PermAccionBits
{
    /**
     * Nombres de las acciones. Usarlos en lugar de literales en `have_perm_activ()`,
     * `have_perm_action()` y `only_perm()`: un literal mal escrito no falla al compilar.
     */
    public const NADA = 'nada';
    public const OCUPADO = 'ocupado';
    public const VER = 'ver';
    public const MODIFICAR = 'modificar';
    public const CREAR = 'crear';
    public const BORRAR = 'borrar';

    /**
     * @return array<string, int>
     */
    public static function map(): array
    {
        return [
            self::NADA => 0,
            self::OCUPADO => 1,
            self::VER => 3,
            self::MODIFICAR => 7,
            self::CREAR => 15,
            self::BORRAR => 31,
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
