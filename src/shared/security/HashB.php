<?php

namespace src\shared\security;

use InvalidArgumentException;

/**
 * HashB — Hash de la capa backend.
 *
 * Rol: firmar y verificar **cápsulas opacas** que autorizan una acción
 * concreta sobre un contexto concreto para un usuario concreto. El
 * frontend **no puede producir** estas cápsulas; solo transportarlas
 * entre una lectura y una mutación posterior.
 *
 * **Asimétrica por diseño**: el método `sign()` solo debe ser invocado
 * desde código de `src/` (controladores HTTP al responder lecturas,
 * casos de uso que emiten tokens de acción). El método `open()` solo
 * se usa desde controladores HTTP de `src/` al recibir mutaciones.
 *
 * Ver `documentacion/hash_arquitectura.md` para el modelo completo
 * y los flujos de uso.
 *
 * **Formato de la cápsula**:
 *
 *     base64url(JSON({a, c, s, x})) . '.' . md5_sig
 *
 *   a: acción autorizada (string)
 *   c: contexto (array asociativo con los ids / valores firmados)
 *   s: session_id en el momento de la firma
 *   x: timestamp unix de caducidad; 0 = sin caducidad
 *
 *   md5_sig = md5(base64url_payload . session_id() . SALT)
 *
 * **Fase 1** (actual): secreto derivado de la sesión PHP (igual que
 * `HashF`, pero con salt distinta para desacoplar firmas). Esto permite
 * migrar sin introducir dependencias nuevas.
 *
 * **Fase posterior**: sustituir `SALT` por un secreto backend-only
 * (env var / `ConfigGlobal`). La firma dejará de variar entre sesiones
 * y la ligadura al usuario recaerá enteramente en el campo `s` del
 * payload. La API pública no cambia.
 */
final class HashB
{
    /**
     * Salt distinta de la que usa `\web\Hash` / `HashF` internamente
     * (`"a+a+"`), para que compartir el `session_id()` no implique
     * compartir firmas entre capas.
     */
    private const SALT = 'b+b+b+';

    /** Segundos de validez por defecto si no se especifica TTL. */
    public const DEFAULT_TTL = 3600;

    /**
     * Firma una cápsula autorizando `$action` sobre `$context`.
     *
     * @param string       $action  Identificador de la acción backend (p.ej. `tarifa_ubi_eliminar`).
     *                              Debe coincidir exactamente con el que pasará el receptor a `open()`.
     * @param array<string,mixed> $context Datos de identidad / contexto que se firman dentro
     *                              de la cápsula. No los pongas como campos sueltos en el DOM.
     * @param int|null     $ttl     Segundos de validez. `null` → {@see self::DEFAULT_TTL}.
     *                              `0` o negativo → sin caducidad (evitar salvo casos puntuales).
     *
     * @throws InvalidArgumentException si `$action` es vacío.
     */
    public static function sign(string $action, array $context = [], ?int $ttl = null): string
    {
        if ($action === '') {
            throw new InvalidArgumentException('HashB::sign requires a non-empty action');
        }

        $ttl = $ttl ?? self::DEFAULT_TTL;
        $exp = ($ttl <= 0) ? 0 : time() + $ttl;

        $payload = [
            'a' => $action,
            'c' => $context,
            's' => self::sessionId(),
            'x' => $exp,
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new InvalidArgumentException('HashB::sign failed to encode context as JSON');
        }

        $encoded = self::base64UrlEncode($json);
        $sig = self::signature($encoded);

        return $encoded . '.' . $sig;
    }

    /**
     * Verifica una cápsula y devuelve el contexto firmado.
     *
     * Comprueba por orden: formato, firma, acción esperada, sesión y caducidad.
     *
     * @param string $capsule        Cápsula tal como la envió el navegador.
     * @param string $expectedAction Acción que el receptor está dispuesto a ejecutar.
     *                               Debe coincidir **exactamente** con la firmada.
     *
     * @return array<string,mixed> El contexto firmado (posiblemente vacío).
     * @throws HashBInvalidException Con {@see HashBInvalidException::getReason()} indicando el motivo.
     */
    public static function open(string $capsule, string $expectedAction): array
    {
        if ($capsule === '') {
            throw new HashBInvalidException('empty capsule', HashBInvalidException::MALFORMED);
        }
        if (substr_count($capsule, '.') !== 1) {
            throw new HashBInvalidException('malformed capsule (expected `payload.sig`)', HashBInvalidException::MALFORMED);
        }

        [$encoded, $sig] = explode('.', $capsule, 2);
        if ($encoded === '' || $sig === '') {
            throw new HashBInvalidException('malformed capsule (empty part)', HashBInvalidException::MALFORMED);
        }

        $expectedSig = self::signature($encoded);
        if (!hash_equals($expectedSig, $sig)) {
            throw new HashBInvalidException('signature mismatch', HashBInvalidException::SIGNATURE_MISMATCH);
        }

        $json = self::base64UrlDecode($encoded);
        if ($json === false) {
            throw new HashBInvalidException('payload is not valid base64url', HashBInvalidException::MALFORMED);
        }

        $payload = json_decode($json, true);
        if (!is_array($payload)
            || !array_key_exists('a', $payload)
            || !array_key_exists('c', $payload)
            || !array_key_exists('s', $payload)
            || !array_key_exists('x', $payload)
        ) {
            throw new HashBInvalidException('payload missing required fields', HashBInvalidException::MALFORMED);
        }

        $actionInPayload = is_string($payload['a']) ? $payload['a'] : '';
        if (!hash_equals($expectedAction, $actionInPayload)) {
            throw new HashBInvalidException('action mismatch', HashBInvalidException::ACTION_MISMATCH);
        }

        $sessionInPayload = is_string($payload['s']) ? $payload['s'] : '';
        if (!hash_equals(self::sessionId(), $sessionInPayload)) {
            throw new HashBInvalidException('session mismatch', HashBInvalidException::SESSION_MISMATCH);
        }

        $exp = is_int($payload['x']) ? $payload['x'] : (int) $payload['x'];
        if ($exp > 0 && $exp < time()) {
            throw new HashBInvalidException('capsule expired', HashBInvalidException::EXPIRED);
        }

        return is_array($payload['c']) ? $payload['c'] : [];
    }

    /**
     * Extrae el contexto sin validar (solo para diagnóstico / debug).
     *
     * **No usar en código de producción.** No verifica firma, sesión ni
     * caducidad. Sirve para logs y mensajes de error cuando queremos saber
     * qué había dentro de una cápsula rechazada.
     *
     * @return array{a:string,c:array,s:string,x:int}|null null si está malformada.
     */
    public static function peekUnsafe(string $capsule): ?array
    {
        if (substr_count($capsule, '.') !== 1) {
            return null;
        }
        [$encoded,] = explode('.', $capsule, 2);
        $json = self::base64UrlDecode($encoded);
        if ($json === false) {
            return null;
        }
        $payload = json_decode($json, true);
        if (!is_array($payload)) {
            return null;
        }
        return [
            'a' => (string) ($payload['a'] ?? ''),
            'c' => is_array($payload['c'] ?? null) ? $payload['c'] : [],
            's' => (string) ($payload['s'] ?? ''),
            'x' => (int) ($payload['x'] ?? 0),
        ];
    }

    private static function signature(string $encodedPayload): string
    {
        return md5($encodedPayload . self::sessionId() . self::SALT);
    }

    private static function sessionId(): string
    {
        $sid = session_id();
        return $sid === false ? '' : $sid;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string|false
    {
        $b64 = strtr($data, '-_', '+/');
        $padding = strlen($b64) % 4;
        if ($padding) {
            $b64 .= str_repeat('=', 4 - $padding);
        }
        return base64_decode($b64, true);
    }

    private function __construct()
    {
        // clase puramente estática
    }
}
