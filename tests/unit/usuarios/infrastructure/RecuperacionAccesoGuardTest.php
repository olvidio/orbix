<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\infrastructure;

use src\shared\config\ConfigGlobal;
use Tests\myTest;

/**
 * Recuperación de contraseña y de 2FA (páginas y endpoints anónimos):
 * - la base de las URL (la del enlace del correo y la del backend al que llama
 *   el frontend) sale de la configuración, nunca de `url_base` de la petición:
 *   con una base ajena el token de recuperación del correo apuntaría a otro
 *   servidor y el frontend llamaría a una URL arbitraria;
 * - el enlace «volver» se escapa;
 * - al usar el enlace se desactiva el 2FA en el esquema del usuario, no en uno fijo.
 */
final class RecuperacionAccesoGuardTest extends myTest
{
    private const RAIZ = __DIR__ . '/../../../../';

    public function test_la_url_publica_sale_de_la_configuracion(): void
    {
        $antes = getenv('ORBIX_PUBLIC_APP_BASE_URL');
        try {
            putenv('ORBIX_PUBLIC_APP_BASE_URL=https://orbix.example.org/app/');
            $this->assertSame('https://orbix.example.org/app', ConfigGlobal::publicAppBaseUrl());
            putenv('ORBIX_PUBLIC_APP_BASE_URL');
            $this->assertSame(rtrim(ConfigGlobal::getWeb(), '/'), ConfigGlobal::publicAppBaseUrl());
        } finally {
            putenv($antes === false ? 'ORBIX_PUBLIC_APP_BASE_URL' : "ORBIX_PUBLIC_APP_BASE_URL=$antes");
        }
    }

    public function test_ningun_fichero_de_recuperacion_toma_url_base_de_la_peticion(): void
    {
        foreach ([
            'src/usuarios/infrastructure/ui/http/controllers/recuperar_2fa_mail.php',
            'src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php',
            'frontend/usuarios/controller/recuperar_2fa.php',
            'frontend/usuarios/controller/recuperar_password.php',
            'frontend/usuarios/controller/ayuda_2fa_reset.php',
        ] as $f) {
            $codigo = (string) file_get_contents(self::RAIZ . $f);
            $this->assertDoesNotMatchRegularExpression("/(filter_input|post|requestString|\\\$_(GET|POST|REQUEST))\\s*\\(?[^;]*'url_base'/", $codigo, $f);
        }
    }

    public function test_el_enlace_volver_se_escapa(): void
    {
        foreach (['ayuda_acceso', 'recuperar_2fa', 'recuperar_password', 'ayuda_2fa_reset'] as $v) {
            $html = (string) file_get_contents(self::RAIZ . "frontend/usuarios/view/$v.phtml");
            $this->assertStringNotContainsString('href="<?= $url_base ?>"', $html, $v);
        }
    }

    public function test_el_enlace_de_2fa_no_usa_un_esquema_fijo(): void
    {
        $codigo = (string) file_get_contents(self::RAIZ . 'frontend/usuarios/controller/recovery.php');
        $this->assertDoesNotMatchRegularExpression('/(UPDATE|FROM)\s+"H-/', $codigo);
    }
}
