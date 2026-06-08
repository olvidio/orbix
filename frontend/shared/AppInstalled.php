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
        if (empty($_SESSION['config']['a_apps'][$nomApp])) {
            return false;
        }
        $id_app = $_SESSION['config']['a_apps'][$nomApp];

        return in_array($id_app, $_SESSION['config']['app_installed'], true);
    }
}
