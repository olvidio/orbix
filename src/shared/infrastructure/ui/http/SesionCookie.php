<?php

namespace src\shared\infrastructure\ui\http;

/**
 * Atributos de la cookie de sesión.
 *
 * PHP reemite la cabecera `Set-Cookie` cada vez que se abre la sesión con un id ya fijado, y la monta
 * con las directivas `session.cookie_*` vigentes en ese momento. Por eso los atributos se fijan como
 * directivas, antes del primer `session_start()` de la petición, y no con `session_set_cookie_params()`
 * solo en la primera visita: así también los llevan las reemisiones y las reaperturas de la sesión.
 *
 * Este fichero no depende del autoload: `index.php` lo carga con `require_once` antes de Composer.
 */
final class SesionCookie
{
    /**
     * @param array<string, mixed> $server
     * @return array<string, string>
     */
    public static function directivas(array $server): array
    {
        return [
            'session.cookie_path' => '/',
            'session.cookie_httponly' => '1',
            'session.cookie_samesite' => 'Lax',
            'session.cookie_secure' => self::esHttps($server) ? '1' : '0',
            'session.use_only_cookies' => '1',
            'session.use_strict_mode' => '1',
        ];
    }

    /**
     * Se llama antes de cada primer `session_start()` de la petición.
     */
    public static function configurar(): void
    {
        if (PHP_SAPI === 'cli' || session_status() === PHP_SESSION_ACTIVE || headers_sent()) {
            return;
        }
        foreach (self::directivas($_SERVER) as $directiva => $valor) {
            ini_set($directiva, $valor);
        }
        foreach (self::cabecerasDeSeguridad() as $cabecera) {
            header($cabecera);
        }
    }

    /**
     * Cabeceras que acompañan a toda respuesta de la aplicación: ninguna página se puede incrustar en
     * un marco de otro sitio (clickjacking). Las de los ficheros estáticos las pone el servidor web.
     *
     * @return list<string>
     */
    public static function cabecerasDeSeguridad(): array
    {
        return [
            'X-Frame-Options: SAMEORIGIN',
            "Content-Security-Policy: frame-ancestors 'self'",
        ];
    }

    /**
     * @param array<string, mixed> $server
     */
    private static function esHttps(array $server): bool
    {
        $https = strtolower((string) ($server['HTTPS'] ?? ''));
        if ($https !== '' && $https !== 'off') {
            return true;
        }

        return strtolower((string) ($server['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
    }
}
