<?php

declare(strict_types=1);

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartasPresentacionListaData;

/**
 * El listado de cartas de presentación monta HTML con textos que escriben los
 * usuarios (persona de contacto, correo, dirección): tienen que ir escapados.
 */
final class CartasPresentacionListaDataEscapadoTest extends TestCase
{
    private function uc(): CartasPresentacionListaData
    {
        // datosACeldas() no usa las dependencias (y UbiTelecoService es final).
        return (new \ReflectionClass(CartasPresentacionListaData::class))->newInstanceWithoutConstructor();
    }

    public function test_los_textos_de_la_carta_y_la_direccion_van_escapados(): void
    {
        $celdas = (new \ReflectionMethod(CartasPresentacionListaData::class, 'datosACeldas'))->invoke($this->uc(), [
            'pres_nom' => '<script>alert(1)</script>', 'pres_telf' => '', 'pres_mail' => 'a"b@x.org', 'direccion' => 'C/ <b>Mayor</b>',
            'nom_sede' => '', 'a_p' => '', 'c_p' => '08001', 'poblacion' => "L'Hospitalet", 'telf' => '',
            'a_direccion' => [['direccion' => '<i>2</i>', 'c_p' => '', 'poblacion' => '', 'telf' => '']],
        ]);
        $this->assertStringNotContainsString('<script>', $celdas);
        $this->assertStringNotContainsString('<b>', $celdas);
        $this->assertStringNotContainsString('<i>', $celdas);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $celdas);
        $this->assertStringContainsString('a&quot;b@x.org', $celdas);
        $this->assertStringContainsString('L&#039;Hospitalet', $celdas);
        $this->assertStringStartsWith('<td class="line-top">', $celdas, 'el marcado propio se conserva');
    }
}
