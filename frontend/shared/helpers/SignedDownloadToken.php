<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\config\AppUrlConfig;
use src\shared\security\SignedDownloadToken as Token;

/**
 * URLs de descarga GET firmadas con HMAC (`tk`), sin HashF (`realFullUrl` / discrepancia emisor–receptor).
 * El token lo genera y lo valida {@see Token} (`src/shared/security`); aquí solo se monta la URL pública.
 *
 * Producción: definir `ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET` (cadena suficientemente larga y aleatoria).
 */
final class SignedDownloadToken
{
    public const SCOPE_NOTAS_ACTA = Token::SCOPE_NOTAS_ACTA;

    public const SCOPE_CERT_EMITIDO = Token::SCOPE_CERT_EMITIDO;

    public const SCOPE_CERT_RECIBIDO = Token::SCOPE_CERT_RECIBIDO;

    public const SCOPE_UBIS_PLANO = Token::SCOPE_UBIS_PLANO;

    private static function buildUrl(string $pathSuffix, string $tk): string
    {
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');

        return $base . $pathSuffix . '?' . http_build_query(['tk' => $tk]);
    }

    public static function urlNotasActa(string $actaId): string
    {
        if ($actaId === '') {
            return '';
        }

        return self::buildUrl('/src/notas/acta_pdf_download', Token::tokenNotasActa($actaId));
    }

    public static function urlCertificadoEmitido(int $idItem): string
    {
        if ($idItem <= 0) {
            return '';
        }

        return self::buildUrl('/src/certificados/certificado_emitido_pdf_download', Token::tokenCertificadoEmitido($idItem));
    }

    public static function urlCertificadoRecibido(int $idItem): string
    {
        if ($idItem <= 0) {
            return '';
        }

        return self::buildUrl('/src/certificados/certificado_recibido_pdf_download', Token::tokenCertificadoRecibido($idItem));
    }

    public static function urlPlanoDireccion(string $objDir, int $idDireccion): string
    {
        if ($objDir === '' || $idDireccion <= 0) {
            return '';
        }

        return self::buildUrl('/src/ubis/plano_download', Token::tokenPlanoDireccion($objDir, $idDireccion));
    }
}
