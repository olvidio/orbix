<?php

declare(strict_types=1);

namespace src\usuarios\domain;

/**
 * Quién puede administrar usuarios, grupos, permisos y roles, según el rol del que lo pide.
 *
 * - Roles 1 a 3 (superadministrador, administrador): administran usuarios, grupos y permisos.
 * - Los roles son comunes a todas las delegaciones: solo el superadministrador (1) los crea, modifica o
 *   borra; el administrador (2) puede cambiar los grupos de menú de un rol.
 * - A un superadministrador solo lo toca otro superadministrador, y solo él asigna ese rol.
 */
final class PermisoAdministrarUsuarios
{
    public const ROLE_SUPERADMIN = 1;
    public const ROLE_ADMIN = 2;
    private const ROLE_MAX_ADMINISTRACION = 3;

    public static function puedeAdministrar(int $miRole): bool
    {
        return $miRole >= self::ROLE_SUPERADMIN && $miRole <= self::ROLE_MAX_ADMINISTRACION;
    }

    public static function puedeModificarRoles(int $miRole): bool
    {
        return $miRole === self::ROLE_SUPERADMIN;
    }

    public static function puedeCambiarGruposDeMenuDeRol(int $miRole): bool
    {
        return $miRole === self::ROLE_SUPERADMIN || $miRole === self::ROLE_ADMIN;
    }

    /** @param int|null $roleDelUsuario rol actual del usuario sobre el que se actúa (null: sin rol o usuario nuevo) */
    public static function puedeTocarUsuarioConRol(int $miRole, ?int $roleDelUsuario): bool
    {
        if (!self::puedeAdministrar($miRole)) {
            return false;
        }

        return $roleDelUsuario !== self::ROLE_SUPERADMIN || $miRole === self::ROLE_SUPERADMIN;
    }

    public static function puedeAsignarRol(int $miRole, int $roleNuevo): bool
    {
        return self::puedeTocarUsuarioConRol($miRole, $roleNuevo);
    }
}
