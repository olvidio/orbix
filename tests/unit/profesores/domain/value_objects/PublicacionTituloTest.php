<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\PublicacionTitulo;
use Tests\myTest;

class PublicacionTituloTest extends myTest
{
    public function test_create_valid_publicacionTitulo()
    {
        $publicacionTitulo = new PublicacionTitulo('test value');
        $this->assertEquals('test value', $publicacionTitulo->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PublicacionTitulo(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_publicacionTitulo_value()
    {
        $publicacionTitulo = new PublicacionTitulo('test value');
        $this->assertEquals('test value', (string)$publicacionTitulo);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $publicacionTitulo = PublicacionTitulo::fromNullableString('test value');
        $this->assertInstanceOf(PublicacionTitulo::class, $publicacionTitulo);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $publicacionTitulo = PublicacionTitulo::fromNullableString(null);
        $this->assertNull($publicacionTitulo);
    }

}
