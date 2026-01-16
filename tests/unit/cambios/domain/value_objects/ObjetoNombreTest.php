<?php

namespace Tests\unit\cambios\domain\value_objects;

use src\cambios\domain\value_objects\ObjetoNombre;
use Tests\myTest;

class ObjetoNombreTest extends myTest
{
    public function test_create_valid_objetoNombre()
    {
        $objetoNombre = new ObjetoNombre('test value');
        $this->assertEquals('test value', $objetoNombre->value());
    }

    public function test_equals_returns_true_for_same_objetoNombre()
    {
        $objetoNombre1 = new ObjetoNombre('test value');
        $objetoNombre2 = new ObjetoNombre('test value');
        $this->assertTrue($objetoNombre1->equals($objetoNombre2));
    }

    public function test_equals_returns_false_for_different_objetoNombre()
    {
        $objetoNombre1 = new ObjetoNombre('test value');
        $objetoNombre2 = new ObjetoNombre('alternative value');
        $this->assertFalse($objetoNombre1->equals($objetoNombre2));
    }

    public function test_to_string_returns_objetoNombre_value()
    {
        $objetoNombre = new ObjetoNombre('test value');
        $this->assertEquals('test value', (string)$objetoNombre);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $objetoNombre = ObjetoNombre::fromNullableString('test value');
        $this->assertInstanceOf(ObjetoNombre::class, $objetoNombre);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $objetoNombre = ObjetoNombre::fromNullableString(null);
        $this->assertNull($objetoNombre);
    }

}
