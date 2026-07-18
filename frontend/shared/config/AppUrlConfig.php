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

    /**
     * URL absoluta para que el navegador llame a un endpoint `/src/*` (AJAX/forms).
     *
     * Bajo prefijos como `/orbixsf` las rutas extensionless `/src/...` a menudo no
     * llegan al front controller; se enrutan por el proxy físico
     * `frontend/shared/controller/src_ajax.php` + PATH_INFO (`…/src_ajax.php/src/…`).
     *
     * Las llamadas server-side {@see \frontend\shared\PostRequest} deben seguir
     * usando la ruta relativa `/src/...` (despacho in-process).
     *
     * @param string $srcPath Ruta `/src/...` (sin query). La query se concatena después: `srcBrowserUrl($path) . '?' . $q`.
     */
    public static function srcBrowserUrl(string $srcPath): string
    {
        $srcPath = '/' . ltrim($srcPath, '/');
        $qPos = strpos($srcPath, '?');
        if ($qPos !== false) {
            throw new \InvalidArgumentException(
                'srcBrowserUrl: pasar solo la ruta /src/...; la query va fuera: '
                . $srcPath
            );
        }
        if (!preg_match('#^/src/[A-Za-z0-9_./-]+$#', $srcPath)) {
            throw new \InvalidArgumentException('srcBrowserUrl: ruta inválida: ' . $srcPath);
        }

        return self::getPublicAppBaseUrl()
            . '/frontend/shared/controller/src_ajax.php'
            . $srcPath;
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
