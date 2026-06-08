<?php

declare(strict_types=1);

namespace src\shared\application;

use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\permisos\domain\PermDl;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;

/**
 * Hidrata permisos de sesión del backend (una vez por login):
 * - menú de delegación (`iPermMenus`, `oPerm`) vía {@see HydrateMenuPermissions}
 * - actividades (`oPermActividades`)
 *
 * En peticiones posteriores solo restaura `oPerm` si el objeto no sobrevivió
 * a la serialización de sesión; no vuelve a consultar BD.
 */
final class HydratePermisosActividades
{
    public const SESSION_HYDRATED_KEY = 'session_perms_hydrated';

    public function execute(): void
    {
        if ($this->isFullyHydrated()) {
            $this->restoreMenuPermObject();
            return;
        }

        (new HydrateMenuPermissions())->execute();
        $this->hydrateActividades();

        $_SESSION[self::SESSION_HYDRATED_KEY] = true;
    }

    /**
     * Fuerza recálculo en el próximo request (p. ej. tras un login nuevo).
     */
    public static function invalidateSessionCache(): void
    {
        unset(
            $_SESSION[self::SESSION_HYDRATED_KEY],
            $_SESSION['iPermMenus'],
            $_SESSION['oPerm'],
            $_SESSION['oPermActividades'],
        );
    }

    private function isFullyHydrated(): bool
    {
        if (empty($_SESSION[self::SESSION_HYDRATED_KEY])) {
            return false;
        }
        if (empty($_SESSION['oPermActividades'])) {
            return false;
        }
        if (ConfigGlobal::is_app_installed('menus') && !isset($_SESSION['iPermMenus'])) {
            return false;
        }

        return true;
    }

    private function restoreMenuPermObject(): void
    {
        if (!array_key_exists('iPermMenus', $_SESSION)) {
            return;
        }
        if (!empty($_SESSION['oPerm'])) {
            return;
        }
        $_SESSION['oPerm'] = new PermDl();
        $_SESSION['oPerm']->setAccion((int) $_SESSION['iPermMenus']);
    }

    private function hydrateActividades(): void
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
