<?php

declare(strict_types=1);

namespace src\usuarios\infrastructure\security;

/**
 * Token del enlace de recuperación de contraseña que se envía por correo.
 *
 * Formato: `base64url(JSON{u, e, x, p}) . '.' . base64url(HMAC-SHA256)`.
 *   u: id_usuario
 *   e: esquema del usuario (H-dlbv, H-dlbf…)
 *   x: caducidad (timestamp unix)
 *   p: huella del hash de la contraseña actual
 *
 * No hace falta guardar nada en la BD: la huella `p` hace que el enlace sirva
 * **una sola vez** (al cambiar la contraseña cambia el hash y el enlace deja de
 * valer) y que pedir otro correo no invalide la contraseña actual.
 *
 * El secreto es el mismo que el de las descargas firmadas
 * (`ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET`), con otro prefijo en el HMAC para que
 * un token de un tipo no valga como del otro.
 */
final class EnlaceRecuperacionPassword
{
    private const PREFIJO = 'orbix.pwd_reset.v1';
    private const ENV_SECRET = 'ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET';

    /** Validez del enlace, en segundos. */
    public const TTL = 1800;

    public static function emitir(int $idUsuario, string $esquema, string $hashPassword, ?int $ahora = null): string
    {
        $claims = [
            'e' => $esquema,
            'p' => self::huella($hashPassword),
            'u' => $idUsuario,
            'x' => ($ahora ?? time()) + self::TTL,
        ];
        $json = json_encode($claims, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        return self::b64($json) . '.' . self::b64(self::firma($json));
    }

    /**
     * Abre el token si la firma es buena y no ha caducado (no comprueba la huella).
     *
     * @return array{u: int, e: string, p: string, x: int}|null
     */
    public static function abrir(string $tk, ?int $ahora = null): ?array
    {
        $partes = explode('.', $tk);
        if (count($partes) !== 2) {
            return null;
        }
        $json = self::unb64($partes[0]);
        if ($json === '' || !hash_equals(self::firma($json), self::unb64($partes[1]))) {
            return null;
        }
        $c = json_decode($json, true);
        if (!is_array($c) || !is_int($c['u'] ?? null) || !is_string($c['e'] ?? null)
            || !is_string($c['p'] ?? null) || !is_int($c['x'] ?? null)) {
            return null;
        }
        if ($c['x'] < ($ahora ?? time())) {
            return null;
        }

        return ['u' => $c['u'], 'e' => $c['e'], 'p' => $c['p'], 'x' => $c['x']];
    }

    /** ¿La contraseña sigue siendo la que había al emitir el enlace? */
    public static function huellaCoincide(string $huella, string $hashPasswordActual): bool
    {
        return hash_equals(self::huella($hashPasswordActual), $huella);
    }

    private static function huella(string $hashPassword): string
    {
        return substr(hash_hmac('sha256', 'p|' . $hashPassword, self::clave()), 0, 32);
    }

    private static function firma(string $json): string
    {
        return hash_hmac('sha256', self::PREFIJO . '|' . $json, self::clave(), true);
    }

    private static function clave(): string
    {
        $env = getenv(self::ENV_SECRET);
        if (is_string($env) && $env !== '') {
            return $env;
        }

        // Respaldo de desarrollo (como SignedDownloadToken): no sustituye a un secreto explícito.
        return hash('sha256', self::PREFIJO . '|' . dirname(__DIR__, 4), true);
    }

    private static function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function unb64(string $b64): string
    {
        $raw = base64_decode(strtr($b64, '-_', '+/'), true);

        return $raw === false ? '' : $raw;
    }
}
