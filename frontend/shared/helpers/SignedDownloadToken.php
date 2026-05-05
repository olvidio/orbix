<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\config\AppUrlConfig;

/**
 * URLs de descarga GET firmadas con HMAC (`tk`), sin HashFront (`realFullUrl` / discrepancia emisor–receptor).
 * Hoy cubre PDFs servidos desde controladores específicos; el alcance `s` puede ampliarse a otros binarios.
 *
 * Producción: definir `ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET` (cadena suficientemente larga y aleatoria).
 */
final class SignedDownloadToken
{
    public const SCOPE_NOTAS_ACTA = 'notas.acta';

    public const SCOPE_CERT_EMITIDO = 'cert.emitido';

    public const SCOPE_CERT_RECIBIDO = 'cert.recibido';

    private const ENV_SECRET = 'ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET';

    private const TTL_SECONDS = 600;

    private const SIGN_PREFIX = 'orbix.signed_dl.v1';

    private static function base64UrlEncode(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $b64): string
    {
        $pad = strlen($b64) % 4;
        if ($pad > 0) {
            $b64 .= str_repeat('=', 4 - $pad);
        }
        $bin = base64_decode(strtr($b64, '-_', '+/'), true);

        return $bin === false ? '' : $bin;
    }

    private static function signingKey(): string
    {
        $fromEnv = getenv(self::ENV_SECRET);
        if (is_string($fromEnv) && $fromEnv !== '') {
            return $fromEnv;
        }

        /** @var non-falsy-string $root */
        $root = dirname(__DIR__, 3);

        return hash('sha256', self::SIGN_PREFIX . '|' . $root, true);
    }

    /** @param array<string, int|string> $claims Debe incluir identificadores (p. ej. `a`, `id`) sin `s` ni `e`. */
    private static function mint(string $scope, array $claims): string
    {
        $exp = time() + self::TTL_SECONDS;
        $body = array_merge(['e' => $exp, 's' => $scope], $claims);
        ksort($body);
        $payloadJson = json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        return self::base64UrlEncode($payloadJson) . '.' . self::signPayload($payloadJson);
    }

    private static function signPayload(string $payloadJson): string
    {
        return self::base64UrlEncode(
            hash_hmac('sha256', self::SIGN_PREFIX . '|' . $payloadJson, self::signingKey(), true)
        );
    }

    private static function buildUrl(string $pathSuffix, string $scope, array $claims): string
    {
        $tk = self::mint($scope, $claims);
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');

        return $base . $pathSuffix . '?' . http_build_query(['tk' => $tk]);
    }

    public static function urlNotasActa(string $actaId): string
    {
        if ($actaId === '') {
            return '';
        }

        return self::buildUrl('/src/notas/acta_pdf_download', self::SCOPE_NOTAS_ACTA, ['a' => $actaId]);
    }

    public static function urlCertificadoEmitido(int $idItem): string
    {
        if ($idItem <= 0) {
            return '';
        }

        return self::buildUrl('/src/certificados/certificado_emitido_pdf_download', self::SCOPE_CERT_EMITIDO, ['id' => $idItem]);
    }

    public static function urlCertificadoRecibido(int $idItem): string
    {
        if ($idItem <= 0) {
            return '';
        }

        return self::buildUrl('/src/certificados/certificado_recibido_pdf_download', self::SCOPE_CERT_RECIBIDO, ['id' => $idItem]);
    }

    /**
     * Parsea `tk`; devuelve `null` si no es válido. No consume en servidor (uso idempotente; caducidad `e`).
     *
     * @return array{s: string, e: int, a?: string, id?: int}|null
     */
    public static function parse(?string $tk): ?array
    {
        if ($tk === null || $tk === '') {
            return null;
        }
        $pos = strpos($tk, '.');
        if ($pos === false || $pos < 1) {
            return null;
        }
        $payloadJson = self::base64UrlDecode(substr($tk, 0, $pos));
        $sigProvidedEnc = substr($tk, $pos + 1);
        if ($payloadJson === '') {
            return null;
        }
        $sigBytes = self::base64UrlDecode($sigProvidedEnc);
        if ($sigBytes === '') {
            return null;
        }

        $expected = hash_hmac('sha256', self::SIGN_PREFIX . '|' . $payloadJson, self::signingKey(), true);
        if (strlen($sigBytes) !== strlen($expected) || !hash_equals($expected, $sigBytes)) {
            return null;
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($payloadJson, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        $exp = (int) ($data['e'] ?? 0);
        if ($exp < time()) {
            return null;
        }

        $scope = (string) ($data['s'] ?? '');
        if ($scope === '') {
            return null;
        }

        if ($scope === self::SCOPE_NOTAS_ACTA) {
            $acta = (string) ($data['a'] ?? '');
            if ($acta === '') {
                return null;
            }

            return ['s' => $scope, 'e' => $exp, 'a' => $acta];
        }

        if ($scope === self::SCOPE_CERT_EMITIDO || $scope === self::SCOPE_CERT_RECIBIDO) {
            $id = (int) ($data['id'] ?? 0);
            if ($id <= 0) {
                return null;
            }

            return ['s' => $scope, 'e' => $exp, 'id' => $id];
        }

        return null;
    }
}
