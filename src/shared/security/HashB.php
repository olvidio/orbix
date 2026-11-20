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
 * Ver `docs/dev/hash_arquitectura.md` para el modelo completo
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
 *   firma = hash_hmac('sha256', base64url_payload . "\0" . session_id(), clave de la sesión)
 *
 * La clave es aleatoria, se crea al empezar la sesión y vive solo en el servidor
 * (`$_SESSION[HashB::CLAVE_SESION]`). El usuario conoce su PHPSESSID y el código, pero no la clave:
 * no puede fabricar cápsulas. Aun así, abrir una cápsula no sustituye a comprobar el permiso de quien
 * la envía: el endpoint que la recibe comprueba también el rol o el permiso que corresponda.
 */
final class HashB
{
    /** Clave de `$_SESSION` donde vive la clave de firma de la sesión. */
    public const CLAVE_SESION = 'hashb_clave';

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
     * ¿Es una cápsula firmada aquí para esta sesión? Solo mira la firma: no dice para qué acción vale ni
     * si ha caducado (eso lo comprueba {@see open()} en el endpoint que la recibe).
     */
    public static function esDeEstaSesion(string $capsule): bool
    {
        if (substr_count($capsule, '.') !== 1) {
            return false;
        }
        [$encoded, $sig] = explode('.', $capsule, 2);

        return $encoded !== '' && $sig !== '' && hash_equals(self::signature($encoded), $sig);
    }

    /**
     * Extrae el contexto sin validar (solo para diagnóstico / debug).
     *
     * **No usar en código de producción.** No verifica firma, sesión ni
     * caducidad. Sirve para logs y mensajes de error cuando queremos saber
     * qué había dentro de una cápsula rechazada.
     *
     * @return array{a: string, c: array<int|string, mixed>, s: string, x: int}|null null si está malformada.
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
        $aRaw = $payload['a'] ?? '';
        $sRaw = $payload['s'] ?? '';
        $xRaw = $payload['x'] ?? 0;
        $cRaw = $payload['c'] ?? null;

        return [
            'a' => is_string($aRaw) ? $aRaw : (is_scalar($aRaw) ? (string) $aRaw : ''),
            'c' => is_array($cRaw) ? $cRaw : [],
            's' => is_string($sRaw) ? $sRaw : (is_scalar($sRaw) ? (string) $sRaw : ''),
            'x' => is_int($xRaw) ? $xRaw : (is_numeric($xRaw) ? (int) $xRaw : 0),
        ];
    }

    private static function signature(string $encodedPayload): string
    {
        return hash_hmac('sha256', $encodedPayload . "\0" . self::sessionId(), self::clave());
    }

    /**
     * Crea la clave de firma de la sesión si no existe. Lo llaman los arranques de la petición justo
     * después de `session_start()`: una clave creada después de `session_write_close()` no se guardaría
     * y las cápsulas firmadas con ella no se abrirían en la petición siguiente.
     */
    public static function asegurarClave(): void
    {
        self::clave();
    }

    /**
     * Cambia la clave de la sesión. Se llama al autenticarse: lo firmado antes de entrar deja de valer.
     */
    public static function renovarClave(): void
    {
        unset($_SESSION[self::CLAVE_SESION]);
        self::clave();
    }

    /**
     * Para otras firmas ligadas a la sesión ({@see SignedDownloadToken}).
     */
    public static function claveDeSesion(): string
    {
        return self::clave();
    }

    /**
     * Clave aleatoria de la sesión. Vive solo en el servidor (`$_SESSION`): el usuario conoce su
     * PHPSESSID y el código, pero no esta clave, así que no puede fabricar cápsulas.
     */
    private static function clave(): string
    {
        $clave = $_SESSION[self::CLAVE_SESION] ?? null;
        if (!is_string($clave) || strlen($clave) < 64) {
            $clave = bin2hex(random_bytes(32));
            $_SESSION[self::CLAVE_SESION] = $clave;
        }

        return $clave;
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
