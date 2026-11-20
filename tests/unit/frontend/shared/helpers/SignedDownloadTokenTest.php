<?php

declare(strict_types=1);

namespace Tests\unit\frontend\shared\helpers;

use frontend\shared\helpers\SignedDownloadToken;
use PHPUnit\Framework\TestCase;
use src\shared\security\SignedDownloadToken as Token;

/**
 * Las URL que firma el frontend (`tk`) las valida el controlador de descarga de `src/`.
 */
final class SignedDownloadTokenTest extends TestCase
{
    private static function tk(string $url): string
    {
        $query = (string) parse_url($url, PHP_URL_QUERY);
        parse_str($query, $params);

        return is_string($params['tk'] ?? null) ? $params['tk'] : '';
    }

    public function test_url_acta_lleva_token_valido_con_alcance_y_acta(): void
    {
        $url = SignedDownloadToken::urlNotasActa('dlb 12/26');

        $this->assertStringContainsString('/src/notas/acta_pdf_download?tk=', $url);
        $parsed = Token::parse(self::tk($url));
        $this->assertNotNull($parsed);
        $this->assertSame(SignedDownloadToken::SCOPE_NOTAS_ACTA, $parsed['s']);
        $this->assertSame('dlb 12/26', $parsed['a'] ?? null);
        $this->assertGreaterThan(time(), $parsed['e']);
    }

    public function test_url_certificados_llevan_id_y_su_alcance(): void
    {
        $emitido = Token::parse(self::tk(SignedDownloadToken::urlCertificadoEmitido(5)));
        $recibido = Token::parse(self::tk(SignedDownloadToken::urlCertificadoRecibido(7)));

        $this->assertSame(SignedDownloadToken::SCOPE_CERT_EMITIDO, $emitido['s'] ?? null);
        $this->assertSame(5, $emitido['id'] ?? null);
        $this->assertSame(SignedDownloadToken::SCOPE_CERT_RECIBIDO, $recibido['s'] ?? null);
        $this->assertSame(7, $recibido['id'] ?? null);
    }

    public function test_url_plano_lleva_tipo_de_direccion_e_id(): void
    {
        $url = SignedDownloadToken::urlPlanoDireccion('DireccionCdcDl', 12);
        $plano = Token::parse(self::tk($url));

        $this->assertStringContainsString('/src/ubis/plano_download?tk=', $url);
        $this->assertSame(SignedDownloadToken::SCOPE_UBIS_PLANO, $plano['s'] ?? null);
        $this->assertSame(12, $plano['id'] ?? null);
        $this->assertSame('DireccionCdcDl', $plano['o'] ?? null);
        $this->assertSame('', SignedDownloadToken::urlPlanoDireccion('', 12));
        $this->assertSame('', SignedDownloadToken::urlPlanoDireccion('DireccionCdcDl', 0));
    }

    public function test_sin_identificador_no_hay_url(): void
    {
        $this->assertSame('', SignedDownloadToken::urlNotasActa(''));
        $this->assertSame('', SignedDownloadToken::urlCertificadoEmitido(0));
        $this->assertSame('', SignedDownloadToken::urlCertificadoRecibido(-1));
    }

    public function test_token_manipulado_o_vacio_no_vale(): void
    {
        $tk = self::tk(SignedDownloadToken::urlCertificadoEmitido(5));
        [$payload, $firma] = explode('.', $tk, 2);
        $otroPayload = rtrim(strtr(base64_encode((string) json_encode(['e' => time() + 600, 'id' => 6, 's' => 'cert.emitido'])), '+/', '-_'), '=');

        $this->assertNull(Token::parse($otroPayload . '.' . $firma));
        $this->assertNull(Token::parse($payload . '.x' . $firma));
        $this->assertNull(Token::parse(''));
        $this->assertNull(Token::parse(null));
        $this->assertNull(Token::parse('sinpunto'));
    }

    public function test_el_token_de_una_sesion_no_vale_en_otra(): void
    {
        $antes = $_SESSION ?? [];
        try {
            $_SESSION[\src\shared\security\HashB::CLAVE_SESION] = str_repeat('a', 64);
            $tk = Token::tokenCertificadoEmitido(5);
            $this->assertNotNull(Token::parse($tk));

            $_SESSION[\src\shared\security\HashB::CLAVE_SESION] = str_repeat('b', 64);
            $this->assertNull(Token::parse($tk));
        } finally {
            $_SESSION = $antes;
        }
    }
}
