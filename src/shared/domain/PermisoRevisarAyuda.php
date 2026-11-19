<?php

declare(strict_types=1);

namespace src\shared\domain;

use src\menus\domain\PermisoMenuBits;

/**
 * Quién puede revisar las consultas hechas a la ayuda: los administradores (permiso de menú
 * `admin_sv` o `admin_sf`). Registrar y valorar una consulta lo hace cualquier usuario.
 *
 * `$permisoMenu` es la suma de los permisos de menú de los grupos del usuario (`iPermMenus`).
 */
final class PermisoRevisarAyuda
{
    public static function puede(int $permisoMenu): bool
    {
        $bits = PermisoMenuBits::map();

        return ($permisoMenu & (($bits['admin_sv'] ?? 0) | ($bits['admin_sf'] ?? 0))) !== 0;
    }
}
