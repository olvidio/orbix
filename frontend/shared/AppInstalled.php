<?php

namespace frontend\shared;

/**
 * Comprueba si una app está instalada usando la sesión (`$_SESSION['config']`),
 * sin importar `ConfigGlobal` en controladores del frontend.
 */
final class AppInstalled
{
    public static function is(string $nomApp): bool
    {
        $config = $_SESSION['config'] ?? null;
        if (!is_array($config)) {
            return false;
        }
        $apps = $config['a_apps'] ?? null;
        if (!is_array($apps) || !isset($apps[$nomApp]) || $apps[$nomApp] === '') {
            return false;
        }
        $idApp = $apps[$nomApp];
        $installed = $config['app_installed'] ?? null;
        if (!is_array($installed)) {
            return false;
        }

        return in_array($idApp, $installed, true);
    }
}
