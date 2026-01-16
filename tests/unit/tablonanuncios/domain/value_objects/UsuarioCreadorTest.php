<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use src\tablonanuncios\domain\value_objects\UsuarioCreador;
use Tests\myTest;

class UsuarioCreadorTest extends myTest
{
    public function test_create_valid_usuarioCreador()
    {
        $usuarioCreador = new UsuarioCreador('test value');
        $this->assertEquals('test value', $usuarioCreador->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UsuarioCreador(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_usuarioCreador()
    {
        $usuarioCreador1 = new UsuarioCreador('test value');
        $usuarioCreador2 = new UsuarioCreador('test value');
        $this->assertTrue($usuarioCreador1->equals($usuarioCreador2));
    }

    public function test_equals_returns_false_for_different_usuarioCreador()
    {
        $usuarioCreador1 = new UsuarioCreador('test value');
        $usuarioCreador2 = new UsuarioCreador('alternative value');
        $this->assertFalse($usuarioCreador1->equals($usuarioCreador2));
    }

    public function test_to_string_returns_usuarioCreador_value()
    {
        $usuarioCreador = new UsuarioCreador('test value');
        $this->assertEquals('test value', (string)$usuarioCreador);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $usuarioCreador = UsuarioCreador::fromNullableString('test value');
        $this->assertInstanceOf(UsuarioCreador::class, $usuarioCreador);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $usuarioCreador = UsuarioCreador::fromNullableString(null);
        $this->assertNull($usuarioCreador);
    }

}
