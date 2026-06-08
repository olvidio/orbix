<?php

declare(strict_types=1);

namespace src\shared\application;

use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;

/**
 * Instancia permisos por actividades en `$_SESSION['oPermActividades']` (una vez por sesión).
 */
final class HydratePermisosActividades
{
    public function execute(): void
    {
        if (!empty($_SESSION['oPermActividades'])) {
            return;
        }

        if (ConfigGlobal::is_app_installed('procesos')) {
            $_SESSION['oPermActividades'] = DependencyResolver::make(
                PermisosActividades::class,
                ['idUsuario' => ConfigGlobal::mi_id_usuario()]
            );
            return;
        }

        $_SESSION['oPermActividades'] = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
    }
}
