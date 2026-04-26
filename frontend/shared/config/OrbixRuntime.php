<?php

namespace frontend\shared\config;

/**
 * Acceso a flags y rutas de arranque definidos en `src` sin `use src\...`
 * en cada controlador del frontend.
 */
final class OrbixRuntime
{
    public static function gettextLanguagesDir(): string
    {
        return \src\shared\config\ConfigGlobal::$dir_languages;
    }

    public static function webdirIsPruebas(): bool
    {
        return \src\shared\config\ConfigGlobal::WEBDIR === 'pruebas';
    }

    public static function isDmz(): bool
    {
        return (bool)\src\shared\config\ServerConf::$dmz;
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
}
