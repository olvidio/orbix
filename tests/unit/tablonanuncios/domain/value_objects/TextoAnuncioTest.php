<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use src\tablonanuncios\domain\value_objects\TextoAnuncio;
use Tests\myTest;

class TextoAnuncioTest extends myTest
{
    public function test_create_valid_textoAnuncio()
    {
        $textoAnuncio = new TextoAnuncio('test value');
        $this->assertEquals('test value', $textoAnuncio->value());
    }

    public function test_equals_returns_true_for_same_textoAnuncio()
    {
        $textoAnuncio1 = new TextoAnuncio('test value');
        $textoAnuncio2 = new TextoAnuncio('test value');
        $this->assertTrue($textoAnuncio1->equals($textoAnuncio2));
    }

    public function test_equals_returns_false_for_different_textoAnuncio()
    {
        $textoAnuncio1 = new TextoAnuncio('test value');
        $textoAnuncio2 = new TextoAnuncio('alternative value');
        $this->assertFalse($textoAnuncio1->equals($textoAnuncio2));
    }

    public function test_to_string_returns_textoAnuncio_value()
    {
        $textoAnuncio = new TextoAnuncio('test value');
        $this->assertEquals('test value', (string)$textoAnuncio);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $textoAnuncio = TextoAnuncio::fromNullableString('test value');
        $this->assertInstanceOf(TextoAnuncio::class, $textoAnuncio);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $textoAnuncio = TextoAnuncio::fromNullableString(null);
        $this->assertNull($textoAnuncio);
    }

}
