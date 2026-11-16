<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\PlanoDocText;
use Tests\myTest;

class PlanoDocTextTest extends myTest
{
    public function test_create_valid_planoDocText()
    {
        $planoDocText = new PlanoDocText('test value');
        $this->assertEquals('test value', $planoDocText->value());
    }

    /**
     * El plano es el contenido de un fichero (una foto, un pdf…): binario, de cualquier tamaño, y se
     * guarda byte a byte, sin recortar espacios, saltos de línea ni bytes nulos de los extremos.
     */
    public function test_el_contenido_binario_se_conserva_byte_a_byte_y_no_tiene_limite_de_tamano(): void
    {
        $contenido = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00" . str_repeat("\x00\xAB", 5000) . "\xFF\xD9\n";

        $this->assertSame($contenido, (new PlanoDocText($contenido))->value());
        $this->assertSame($contenido, PlanoDocText::fromNullableString($contenido)?->value());
    }

    public function test_vacio_no_es_un_plano(): void
    {
        $this->assertNull(PlanoDocText::fromNullableString(''));
        $this->expectException(\InvalidArgumentException::class);
        new PlanoDocText('');
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $planoDocText = PlanoDocText::fromNullableString('test value');
        $this->assertInstanceOf(PlanoDocText::class, $planoDocText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $planoDocText = PlanoDocText::fromNullableString(null);
        $this->assertNull($planoDocText);
    }

}
