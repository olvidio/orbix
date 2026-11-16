<?php

declare(strict_types=1);

namespace Tests\unit\cambios\infrastructure\mail;

use src\cambios\infrastructure\mail\AvisosCambiosMailHtml;
use Tests\myTest;

final class AvisosCambiosMailHtmlTest extends myTest
{
    public function test_una_fila_por_cambio_con_las_celdas_en_orden_y_escapadas(): void
    {
        $html = (new AvisosCambiosMailHtml())->cuerpo(
            'Tabla de cambios',
            ['Fecha', 'Quien', 'Cambio'],
            [
                1 => [1 => '4/3/2026 10:00', 2 => 'sv', 3 => 'García & López: cambia <fecha>'],
                2 => [1 => '5/3/2026 11:00', 2 => 'sf', 3 => 'otro'],
            ]
        );

        $this->assertStringStartsWith('<html><head><title>Tabla de cambios</title></head><body>', $html);
        $this->assertSame(3, substr_count($html, '<th class=cabecera>'));
        $this->assertSame(2, substr_count($html, '</tr>' . "\n"));
        $this->assertStringContainsString('<td>4/3/2026 10:00</td><td>sv</td><td>García &amp; López: cambia &lt;fecha&gt;</td>', $html);
        $this->assertStringEndsWith('</tbody></table></body></html>', $html);
    }
}
