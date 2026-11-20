<?php

declare(strict_types=1);

namespace Tests\unit\frontend\usuarios;

use Tests\myTest;

/**
 * El formulario de entrada se pinta antes de autenticarse y con datos de la petición (usuario tecleado,
 * idioma de la cookie, esquema): ninguno sale del atributo o del bloque de script en que se escribe.
 */
final class LoginFormEscapadoTest extends myTest
{
    /** @param array<string, mixed> $campos */
    private function pintar(array $campos): string
    {
        $campos += ['error' => 1, 'ubicacion' => 'sv', 'esquema_web' => '', 'DesplRegiones' => '<select name="esquema"></select>',
            'idioma' => 'es_ES.UTF-8', 'username' => '', 'url' => 'http://orbix.test/orbix'];
        extract($campos);
        ob_start();
        try {
            include dirname(__DIR__, 4) . '/frontend/usuarios/view/login_form.phtml';
        } finally {
            $html = (string) ob_get_clean();
        }

        return $html;
    }

    public function test_el_usuario_tecleado_no_sale_de_su_atributo(): void
    {
        $html = $this->pintar(['username' => 'zz"><x-aud y=1>']);

        $this->assertStringNotContainsString('<x-aud', $html);
        $this->assertStringContainsString('value="zz&quot;&gt;&lt;x-aud y=1&gt;"', $html);
    }

    public function test_idioma_esquema_ubicacion_y_url_tampoco(): void
    {
        $html = $this->pintar([
            'idioma' => '"><x-idi>',
            'esquema_web' => '"><x-esq>',
            'ubicacion' => '<x-ubi>',
            'url' => '";</script><x-url>',
        ]);

        foreach (['<x-idi', '<x-esq', '<x-ubi', '<x-url'] as $etiqueta) {
            $this->assertStringNotContainsString($etiqueta, $html);
        }
    }
}
