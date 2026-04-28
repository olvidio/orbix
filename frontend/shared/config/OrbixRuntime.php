<?php

namespace frontend\shared\config;

/**
 * Acceso a flags y rutas de arranque definidos en `src` sin `use src\...`
 * en cada controlador del frontend.
 *
 * El flag de depuración del frontend (`isDebug` / `isDebugMode`) se resuelve
 * solo por entorno (`ORBIX_FRONT_DEBUG`), sin leer `ServerConf` ni `ConfigGlobal`.
 */
final class OrbixRuntime
{
    /** Activa trazas y mensajes extra en código del árbol `frontend/`. Valores truthy: 1, true, yes, on (insensible a mayúsculas). */
    public const ENV_FRONT_DEBUG = 'ORBIX_FRONT_DEBUG';

    public static function gettextLanguagesDir(): string
    {
        return \src\shared\config\ConfigGlobal::$dir_languages;
    }

    public static function webdirIsPruebas(): bool
    {
        return \src\shared\config\ConfigGlobal::WEBDIR === 'pruebas';
    }

    public static function webdir(): string
    {
        return (string)\src\shared\config\ConfigGlobal::WEBDIR;
    }

    /** @return mixed id de rol en sesión (mismo criterio que `ConfigGlobal::mi_id_role()`). */
    public static function miIdRole()
    {
        return \src\shared\config\ConfigGlobal::mi_id_role();
    }

    public static function webPortSf(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$web_port_sf;
    }

    public static function webPort(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$web_port;
    }

    public static function webPath(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$web_path;
    }

    public static function webServer(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$web_server;
    }

    public static function getWebPort(): string
    {
        return (string)\src\shared\config\ConfigGlobal::getWebPort();
    }

    public static function isDmz(): bool
    {
        return (bool)\src\shared\config\ServerConf::$dmz;
    }

    /**
     * Raíz del proyecto en el filesystem del proceso PHP (mismo valor que `ConfigGlobal::$directorio`
     * y, en instalación típica, `ServerConf::DIR`).
     */
    public static function dir(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$directorio;
    }

    public static function servidor(): string
    {
        return (string)\src\shared\config\ServerConf::SERVIDOR;
    }

    public static function miRegionDl(): string
    {
        return (string)\src\shared\config\ConfigGlobal::mi_region_dl();
    }

    public static function dirEstilos(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$dir_estilos;
    }

    public static function dirLibs(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$dir_libs;
    }

    public static function miAmbito(): string
    {
        return (string)\src\shared\config\ConfigGlobal::mi_ambito();
    }

    public static function miRegion(): string
    {
        return (string)\src\shared\config\ConfigGlobal::mi_region();
    }

    public static function miDele(): string
    {
        return (string)\src\shared\config\ConfigGlobal::mi_dele();
    }

    /** @param string $isfsv igual que en `ConfigGlobal::mi_delef` */
    public static function miDelef(string $isfsv = ''): string
    {
        return (string)\src\shared\config\ConfigGlobal::mi_delef($isfsv);
    }

    public static function isLocaleUs(): bool
    {
        return \src\shared\config\ConfigGlobal::is_locale_us();
    }

    /** @return int 1: sv, 2: sf */
    public static function miSfsv(): int
    {
        return \src\shared\config\ConfigGlobal::mi_sfsv();
    }

    public static function getWebIcons(): string
    {
        return (string)\src\shared\config\ConfigGlobal::getWeb_icons();
    }

    /** Valor de `$_SESSION['session_auth']['role_pau']`. */
    public static function miRolePau()
    {
        return \src\shared\config\ConfigGlobal::mi_role_pau();
    }

    public static function miIdioma(): string
    {
        return (string)\src\shared\config\ConfigGlobal::mi_Idioma();
    }

    public static function isDebug(): bool
    {
        return self::parseEnvBool(getenv(self::ENV_FRONT_DEBUG));
    }

    /** Alias de {@see self::isDebug()} para alinear el nombre con otros sitios del código. */
    public static function isDebugMode(): bool
    {
        return self::isDebug();
    }

    private static function parseEnvBool(string|false $raw): bool
    {
        if ($raw === false || $raw === '') {
            return false;
        }

        $v = strtolower(trim((string)$raw));

        return in_array($v, ['1', 'true', 'yes', 'on'], true);
    }
}
