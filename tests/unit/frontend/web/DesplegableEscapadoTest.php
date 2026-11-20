<?php

declare(strict_types=1);

namespace Tests\unit\frontend\web;

use frontend\shared\web\Desplegable;
use Tests\myTest;

/**
 * Las opciones de un desplegable son datos (nombres de centros, personas, actividades): ni la etiqueta
 * ni el valor salen como HTML (revisión de seguridad del 2026-11-20, XSS-05).
 */
final class DesplegableEscapadoTest extends myTest
{
    public function test_etiqueta_y_valor_de_cada_opcion_van_escapados(): void
    {
        $html = Desplegable::desdeOpciones(
            ['1" x="y' => 'A</option></select><img src=x onerror=alert(1)>', '2' => 'Año &amp; día &nbsp;'],
            'n',
            false
        )->desplegable();

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('value="1" x="y"', $html);
        $this->assertStringContainsString('value="1&quot; x=&quot;y"', $html);
        $this->assertStringContainsString('>Año &amp; día &nbsp;</option>', $html, 'las entidades ya escritas se respetan');
        $this->assertSame(1, substr_count($html, '</select>'));
    }

    public function test_nombre_clase_y_accion_no_salen_de_su_atributo(): void
    {
        $d = Desplegable::desdeOpciones(['1' => 'uno'], 'n"><x-aud>', true);
        $d->setClase('c"><x-aud>');
        $d->setAction('fnjs_x("a")');
        $d->setValBlanco('"><x-aud>');
        $html = $d->desplegable();

        $this->assertStringNotContainsString('<x-aud', $html);
        $this->assertStringContainsString('onChange="fnjs_x(&quot;a&quot;)"', $html);
    }

    public function test_las_casillas_tambien(): void
    {
        $d = Desplegable::desdeOpciones(['1"><x-aud>' => '<x-aud>etiqueta'], 'n', false);

        $this->assertStringNotContainsString('<x-aud', $d->cuadros_check());
    }
}
