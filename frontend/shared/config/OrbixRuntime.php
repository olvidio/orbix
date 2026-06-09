<?php

namespace frontend\shared\config;

/**
 * Acceso a flags y rutas de arranque definidos en `src` sin `use src\...`
 * en cada controlador del frontend.
 *
 * Equivalentes a rutas en `ConfigGlobal` (entre otras):
 * - {@see \src\shared\config\ConfigGlobal::$directorio} → {@see self::dir()}
 * - {@see \src\shared\config\ConfigGlobal::$dir_estilos} → {@see self::dirEstilos()} / {@see self::dir_estilos()}
 * - {@see \src\shared\config\ConfigGlobal::$dir_libs} → {@see self::dirLibs()}
 * - {@see \src\shared\config\ConfigGlobal::$dir_languages} → {@see self::gettextLanguagesDir()}
 * - {@see \src\shared\config\ConfigGlobal::getWeb()} → {@see self::getWeb()}
 * - {@see \src\shared\config\ConfigGlobal::getWeb_public()} → {@see self::getWebPublic()}
 * - {@see \src\shared\config\ConfigGlobal::getWeb_NodeScripts()} → {@see self::getWebNodeScripts()}
 * - {@see \src\shared\config\ConfigGlobal::getWeb_scripts()} → {@see self::getWebScripts()}
 * - {@see \src\shared\config\ConfigGlobal::$dir_scripts} → {@see self::dirScripts()}
 * - {@see \src\shared\config\ConfigGlobal::getWeb_icons()} → {@see self::getWebIcons()}
 * - {@see \src\shared\config\ConfigGlobal::is_app_installed()} → {@see self::isAppInstalled()}
 * - {@see \src\shared\config\ConfigGlobal::mi_sfsv()} → {@see self::miSfsv()}
 * - {@see \src\shared\config\ConfigGlobal::mi_usuario()} → {@see self::miUsuario()}
 * - {@see \src\shared\config\ConfigGlobal::mi_region_dl()} → {@see self::miRegionDl()}
 *
 * El flag de depuración del frontend (`isDebug` / `isDebugMode`) se resuelve
 * solo por entorno (`ORBIX_FRONT_DEBUG`), sin leer `ServerConf` ni `ConfigGlobal`.
 */
final class OrbixRuntime
{
    /** Activa trazas y mensajes extra en código del árbol `frontend/`. Valores truthy: 1, true, yes, on (insensible a mayúsculas). */
    public const ENV_FRONT_DEBUG = 'ORBIX_FRONT_DEBUG';

    /**
     * Digrafos latinos → entidades HTML para impresiones / PDF.
     * Equivalente histórico a `Config::$replace` en `ConfigSnapshot::$replace` (ahora retirado).
     *
     * @var array<string, string>
     */
    private const LATIN_HTML_ENTITY_REPLACE = [
        'AE' => '&#0198;',
        'Ae' => '&#0198;',
        'ae' => '&#0230;',
        'aE' => '&#0230;',
        'OE' => '&#0338;',
        'Oe' => '&#0338;',
        'oe' => '&#0339;',
        'oE' => '&#0339;',
    ];

    /**
     * @return array<string, string>
     */
    public static function latinHtmlEntityReplaceMap(): array
    {
        return self::LATIN_HTML_ENTITY_REPLACE;
    }

    public static function gettextLanguagesDir(): string
    {
        return \src\shared\config\ConfigGlobal::$dir_languages;
    }

    public static function webdirIsPruebas(): bool
    {
        return \src\shared\config\ConfigGlobal::esEntornoPruebas();
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

    /** Entorno de pruebas (`/pruebas` o `/pruebassf`). Equiv. comprobación histórica sobre `$web_path`. */
    public static function isPruebasWebPath(): bool
    {
        $path = self::webPath();

        return $path === '/pruebas' || $path === '/pruebassf';
    }

    public static function webServer(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$web_server;
    }

    public static function getWebPort(): string
    {
        return (string)\src\shared\config\ConfigGlobal::getWebPort();
    }

    /**
     * URL base HTTP (`$web_server` + puerto efectivo + `getWebPath()`). Equiv. {@see \src\shared\config\ConfigGlobal::getWeb()}.
     */
    public static function getWeb(): string
    {
        return (string)\src\shared\config\ConfigGlobal::getWeb();
    }

    /** URL base HTTP + `/public`. Equiv. {@see \src\shared\config\ConfigGlobal::getWeb_public()}. */
    public static function getWebPublic(): string
    {
        return (string)\src\shared\config\ConfigGlobal::getWeb_public();
    }

    /**
     * URL base HTTP + `/node_modules`. Equiv. {@see \src\shared\config\ConfigGlobal::getWeb_NodeScripts()}.
     */
    public static function getWebNodeScripts(): string
    {
        return (string)\src\shared\config\ConfigGlobal::getWeb_NodeScripts();
    }

    /** URL base HTTP + `/scripts`. Equiv. {@see \src\shared\config\ConfigGlobal::getWeb_scripts()}. */
    public static function getWebScripts(): string
    {
        return (string)\src\shared\config\ConfigGlobal::getWeb_scripts();
    }

    /**
     * Ruta absoluta al árbol `scripts/` (filesystem). Mismo valor que {@see \src\shared\config\ConfigGlobal::$dir_scripts}.
     */
    public static function dirScripts(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$dir_scripts;
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

    /** Nombre de usuario en sesión. Equiv. {@see \src\shared\config\ConfigGlobal::mi_usuario()}. */
    public static function miUsuario(): string
    {
        return (string)\src\shared\config\ConfigGlobal::mi_usuario();
    }

    /**
     * Ruta absoluta al directorio de estilos `.css.php` / `colores.php` (filesystem).
     * Mismo valor que {@see \src\shared\config\ConfigGlobal::$dir_estilos}.
     */
    public static function dirEstilos(): string
    {
        return (string)\src\shared\config\ConfigGlobal::$dir_estilos;
    }

    /**
     * Alias de {@see self::dirEstilos()} con el mismo identificador que `ConfigGlobal::$dir_estilos`.
     */
    public static function dir_estilos(): string
    {
        return self::dirEstilos();
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

    /**
     * URL base HTTP + `/images`. Equiv. {@see \src\shared\config\ConfigGlobal::getWeb_icons()}.
     */
    public static function getWebIcons(): string
    {
        return (string)\src\shared\config\ConfigGlobal::getWeb_icons();
    }

    /** Equiv. {@see \src\shared\config\ConfigGlobal::is_app_installed()} (nombre lógico de la app). */
    public static function isAppInstalled(string $nom_app): bool
    {
        return \src\shared\config\ConfigGlobal::is_app_installed($nom_app);
    }

    /** Valor de `$_SESSION['session_auth']['role_pau']`. */
    public static function miRolePau(): string
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
