<?php

declare(strict_types=1);

namespace frontend\shared\session;

/**
 * Acceso tipado a `$_SESSION['oConfig']` sin `use src\...` en callers.
 * Único punto del frontend que conoce {@see \src\configuracion\domain\value_objects\ConfigSnapshot}.
 */
final class SessionConfig
{
    public static function isPresent(): bool
    {
        $oConfig = $_SESSION['oConfig'] ?? null;

        return $oConfig instanceof \src\configuracion\domain\value_objects\ConfigSnapshot;
    }

    private static function snapshot(): ?\src\configuracion\domain\value_objects\ConfigSnapshot
    {
        $oConfig = $_SESSION['oConfig'] ?? null;

        return $oConfig instanceof \src\configuracion\domain\value_objects\ConfigSnapshot
            ? $oConfig
            : null;
    }

    public static function getMesFinStgr(int $default = 6): int
    {
        return self::snapshot()?->getMesFinStgr() ?? $default;
    }

    public static function getMesFinCrt(int $default = 12): int
    {
        return self::snapshot()?->getMesFinCrt() ?? $default;
    }

    public static function getNomRegionLatin(string $default = ''): string
    {
        return self::snapshot()?->getNomRegionLatin() ?? $default;
    }

    public static function getNotaMax(string $default = '0'): string
    {
        return self::snapshot()?->getNotaMax() ?? $default;
    }

    public static function getDiaIniStgr(int $default = 0): int
    {
        return self::snapshot()?->getDiaIniStgr() ?? $default;
    }

    public static function getMesIniStgr(int $default = 0): int
    {
        return self::snapshot()?->getMesIniStgr() ?? $default;
    }

    public static function getDiaFinStgr(int $default = 0): int
    {
        return self::snapshot()?->getDiaFinStgr() ?? $default;
    }

    public static function getDiaIniCrt(int $default = 0): int
    {
        return self::snapshot()?->getDiaIniCrt() ?? $default;
    }

    public static function getMesIniCrt(int $default = 0): int
    {
        return self::snapshot()?->getMesIniCrt() ?? $default;
    }

    public static function getDiaFinCrt(int $default = 0): int
    {
        return self::snapshot()?->getDiaFinCrt() ?? $default;
    }

    public static function getIdiomaDefault(string $default = ''): string
    {
        return self::snapshot()?->getIdioma_default() ?? $default;
    }

    public static function isJefeCalendario(string $username = ''): bool
    {
        return self::snapshot()?->is_jefeCalendario($username) ?? false;
    }

    public static function getGestionCalendario(?string $default = null): ?string
    {
        return self::snapshot()?->getGestionCalendario() ?? $default;
    }

    /**
     * @param 'est'|'crt' $tipo
     */
    public static function anyFinalCurs(string $tipo = 'est'): int
    {
        return self::snapshot()?->any_final_curs($tipo) ?? 0;
    }
}
