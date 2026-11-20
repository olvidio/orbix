<?php

declare(strict_types=1);

namespace Tests\unit\frontend\usuarios;

use Tests\myTest;

/**
 * Las páginas de ayuda de acceso se abren sin sesión y pintan datos de la cuenta que se indique (el correo,
 * que cada usuario se pone a sí mismo): nada de eso sale como HTML. Lo mismo en los formularios de la
 * ficha de usuario (revisión de seguridad del 2026-11-20, XSS-01).
 */
final class AyudaDeAccesoEscapadaTest extends myTest
{
    private const MALO = '"*@x.<x-aud onload=1>"';

    /** @param array<string, mixed> $campos */
    private function pintar(string $vista, array $campos): string
    {
        extract($campos);
        ob_start();
        try {
            include dirname(__DIR__, 4) . '/frontend/usuarios/view/' . $vista;
        } finally {
            $html = (string) ob_get_clean();
        }

        return $html;
    }

    public function test_ayuda_de_2fa(): void
    {
        $html = $this->pintar('ayuda_2fa_reset.phtml', [
            'error_txt' => '', 'linkEnviarMail2fa' => 'recuperar_2fa.php?username="><x-aud>', 'emailOfuscado' => self::MALO, 'url_base' => 'http://orbix.test/',
        ]);

        $this->assertStringNotContainsString('<x-aud', $html);
        $this->assertStringContainsString('&lt;x-aud', $html);
    }

    public function test_ayuda_de_acceso(): void
    {
        $html = $this->pintar('ayuda_acceso.phtml', [
            'error_txt' => '', 'emailOfuscado' => self::MALO, 'mail_admin' => 'a@b.es, "><x-aud>"@c.es',
            'linkEnviarMailPasswd' => 'x?u="><x-aud>', 'linkAyuda2FA' => 'y?u="><x-aud>', 'url_base' => 'http://orbix.test/',
        ]);

        $this->assertStringNotContainsString('<x-aud', $html);
    }

    public function test_los_formularios_de_usuario_escapan_el_correo(): void
    {
        foreach (['usuario_form.phtml', 'usuario_form_mail.phtml'] as $vista) {
            $codigo = (string) file_get_contents(dirname(__DIR__, 4) . '/frontend/usuarios/view/' . $vista);
            $this->assertStringNotContainsString('value="<?= $email ?>"', $codigo, $vista);
        }
    }
}
