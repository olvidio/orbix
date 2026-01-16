<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\TipoActivNombre;
use Tests\myTest;

class TipoActivNombreTest extends myTest
{
    public function test_create_valid_tipoActivNombre()
    {
        $tipoActivNombre = new TipoActivNombre('test value');
        $this->assertEquals('test value', $tipoActivNombre->value());
    }

    public function test_equals_returns_true_for_same_tipoActivNombre()
    {
        $tipoActivNombre1 = new TipoActivNombre('test value');
        $tipoActivNombre2 = new TipoActivNombre('test value');
        $this->assertTrue($tipoActivNombre1->equals($tipoActivNombre2));
    }

    public function test_equals_returns_false_for_different_tipoActivNombre()
    {
        $tipoActivNombre1 = new TipoActivNombre('test value');
        $tipoActivNombre2 = new TipoActivNombre('alternative value');
        $this->assertFalse($tipoActivNombre1->equals($tipoActivNombre2));
    }

    public function test_to_string_returns_tipoActivNombre_value()
    {
        $tipoActivNombre = new TipoActivNombre('test value');
        $this->assertEquals('test value', (string)$tipoActivNombre);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoActivNombre = TipoActivNombre::fromNullableString('test value');
        $this->assertInstanceOf(TipoActivNombre::class, $tipoActivNombre);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoActivNombre = TipoActivNombre::fromNullableString(null);
        $this->assertNull($tipoActivNombre);
    }

}
