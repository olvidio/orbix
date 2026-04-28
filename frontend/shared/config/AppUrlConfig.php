<?php

namespace frontend\shared\config;

/**
 * Orígenes HTTP para la app pública y la API. En despliegue monolito ambos
 * coinciden con {@see OrbixRuntime::getWeb()}. Con front y API en hosts distintos,
 * definir ORBIX_PUBLIC_APP_BASE_URL y ORBIX_API_BASE_URL.
 */
class AppUrlConfig
{
    public const ENV_PUBLIC_APP_BASE = 'ORBIX_PUBLIC_APP_BASE_URL';

    public const ENV_API_BASE = 'ORBIX_API_BASE_URL';

    public static function getPublicAppBaseUrl(): string
    {
        $fromEnv = self::readEnv(self::ENV_PUBLIC_APP_BASE);
        if ($fromEnv !== null) {
            return $fromEnv;
        }

        return self::normalizeBaseUrl(OrbixRuntime::getWeb());
    }

    public static function getApiBaseUrl(): string
    {
        $fromEnv = self::readEnv(self::ENV_API_BASE);
        if ($fromEnv !== null) {
            return $fromEnv;
        }

        return self::getPublicAppBaseUrl();
    }

    /** Base HTTP para `/node_modules` (alineado con {@see OrbixRuntime::getWebNodeScripts()}). */
    public static function getNodeModulesBaseUrl(): string
    {
        return self::normalizeBaseUrl(OrbixRuntime::getWebNodeScripts());
    }

    private static function readEnv(string $name): ?string
    {
        $v = getenv($name);
        if ($v === false || $v === '') {
            return null;
        }

        return self::normalizeBaseUrl((string)$v);
    }

    private static function normalizeBaseUrl(string $url): string
    {
        return rtrim($url, '/');
    }
}
