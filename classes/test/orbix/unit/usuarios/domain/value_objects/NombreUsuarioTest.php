<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\NombreUsuario;
use Tests\myTest;

class NombreUsuarioTest extends myTest
{
    public function test_create_valid_nombre_usuario()
    {
        $nombreUsuario = new NombreUsuario('John Doe');
        $this->assertEquals('John Doe', $nombreUsuario->value());
    }

    public function test_empty_nombre_usuario_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User name cannot be empty');
        new NombreUsuario('');
    }

    public function test_equals_returns_true_for_same_nombre_usuario()
    {
        $nombreUsuario1 = new NombreUsuario('John Doe');
        $nombreUsuario2 = new NombreUsuario('John Doe');
        $this->assertTrue($nombreUsuario1->equals($nombreUsuario2));
    }

    public function test_equals_returns_false_for_different_nombre_usuario()
    {
        $nombreUsuario1 = new NombreUsuario('John Doe');
        $nombreUsuario2 = new NombreUsuario('Jane Smith');
        $this->assertFalse($nombreUsuario1->equals($nombreUsuario2));
    }

    public function test_to_string_returns_nombre_usuario_value()
    {
        $nombreUsuario = new NombreUsuario('John Doe');
        $this->assertEquals('John Doe', (string)$nombreUsuario);
    }
}