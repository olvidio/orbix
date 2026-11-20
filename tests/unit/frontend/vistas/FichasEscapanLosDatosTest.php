<?php

declare(strict_types=1);

namespace Tests\unit\frontend\vistas;

use PHPUnit\Framework\TestCase;

/**
 * Los datos de una persona o de un centro (nombre, profesión, teléfonos, observaciones) los escribe un
 * usuario y los ven otros: en la ficha salen como texto (revisión de seguridad del 2026-11-20, XSS-04).
 */
final class FichasEscapanLosDatosTest extends TestCase
{
    private const MALO = '"><x-aud onload=1>\'';

    /** @param array<string, mixed> $campos */
    private function pintar(string $vista, array $campos): string
    {
        $oPosicion = new class {
            public function mostrarNavAtras(int $n = 1): string
            {
                return '';
            }
        };
        extract($campos);
        ob_start();
        try {
            include dirname(__DIR__, 4) . '/frontend/' . $vista;
        } finally {
            $html = (string) ob_get_clean();
        }

        return $html;
    }

    public function test_la_ficha_de_persona(): void
    {
        $campos = array_fill_keys(
            ['titulo', 'dl', 'ctr', 'f_nacimiento', 'santo', 'celebra', 'profesion', 'stgr', 'telfs', 'mails', 'godossiers', 'gohome', 'go_ficha'],
            self::MALO
        );
        $html = $this->pintar('personas/view/home_persona.phtml', $campos + ['aviso' => '', 'lista_dossiers_html' => '<p id="dossiers"></p>']);

        $this->assertStringNotContainsString('<x-aud', $html);
        $this->assertStringContainsString('&lt;x-aud', $html);
        $this->assertStringContainsString('<p id="dossiers"></p>', $html, 'El HTML que monta el controlador se respeta');
    }

    public function test_la_cabecera_de_una_lista_de_asistentes(): void
    {
        $codigo = (string) file_get_contents(dirname(__DIR__, 4) . '/frontend/asistentes/view/lista_asistentes.phtml');

        $this->assertStringNotContainsString('<?= $nom_activ; ?>', $codigo);
    }
}
