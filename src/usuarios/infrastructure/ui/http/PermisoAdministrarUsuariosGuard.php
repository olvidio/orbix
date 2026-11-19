<?php

declare(strict_types=1);

namespace src\usuarios\infrastructure\ui\http;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\PermisoAdministrarUsuarios;

/**
 * Comprobación de permiso al principio de cada controlador de administración de `src/usuarios`
 * (usuarios, grupos, permisos y roles). Va en el controlador y no en `routes.php` porque `PostRequest`
 * despacha los controladores en el mismo proceso sin pasar por las rutas. Tanto el endpoint que entrega
 * la cápsula `HashB` como el que la recibe la llaman: abrir la cápsula no basta.
 */
final class PermisoAdministrarUsuariosGuard
{
    public static function permite(): bool
    {
        return PermisoAdministrarUsuarios::puedeAdministrar(ConfigGlobal::mi_id_role());
    }

    public static function permiteModificarRoles(): bool
    {
        return PermisoAdministrarUsuarios::puedeModificarRoles(ConfigGlobal::mi_id_role());
    }

    public static function permiteCambiarGruposDeMenuDeRol(): bool
    {
        return PermisoAdministrarUsuarios::puedeCambiarGruposDeMenuDeRol(ConfigGlobal::mi_id_role());
    }

    /** El usuario sobre el que se actúa no es un superadministrador, o lo soy yo. Un id que no existe se admite. */
    public static function permiteSobreUsuario(int $idUsuario): bool
    {
        $roleDelUsuario = null;
        if ($idUsuario > 0) {
            $usuario = DependencyResolver::get(UsuarioRepositoryInterface::class)->findById($idUsuario);
            $roleDelUsuario = $usuario?->getId_role();
        }

        return PermisoAdministrarUsuarios::puedeTocarUsuarioConRol(ConfigGlobal::mi_id_role(), $roleDelUsuario);
    }

    public static function permiteAsignarRol(int $roleNuevo): bool
    {
        return PermisoAdministrarUsuarios::puedeAsignarRol(ConfigGlobal::mi_id_role(), $roleNuevo);
    }
}
