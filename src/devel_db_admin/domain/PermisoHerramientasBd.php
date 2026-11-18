<?php

declare(strict_types=1);

namespace src\devel_db_admin\domain;

use src\menus\domain\PermisoMenuBits;

/**
 * Quién puede usar las herramientas de base de datos: los mismos que las ven en el menú. Las entradas de
 * esquemas, tablas de apps y mover tabla piden el permiso de menú `admin_sv`; «actualizar DB» (las
 * migraciones) pide `admin_sv` o `admin_sf` (la serie sf la lanza un administrador sf).
 *
 * `$permisoMenu` es la suma de los permisos de menú de los grupos del usuario (`iPermMenus`).
 */
final class PermisoHerramientasBd
{
    public static function puedeEsquemas(int $permisoMenu): bool
    {
        return ($permisoMenu & self::bit('admin_sv')) !== 0;
    }

    public static function puedeMigraciones(int $permisoMenu): bool
    {
        return ($permisoMenu & (self::bit('admin_sv') | self::bit('admin_sf'))) !== 0;
    }

    private static function bit(string $nombre): int
    {
        return PermisoMenuBits::map()[$nombre] ?? 0;
    }
}
