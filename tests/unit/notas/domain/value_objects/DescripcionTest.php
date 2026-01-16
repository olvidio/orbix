<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Descripcion;
use Tests\myTest;

class DescripcionTest extends myTest
{
    public function test_create_valid_descripcion()
    {
        $descripcion = new Descripcion('test value');
        $this->assertEquals('test value', $descripcion->value());
    }

    public function test_to_string_returns_descripcion_value()
    {
        $descripcion = new Descripcion('test value');
        $this->assertEquals('test value', (string)$descripcion);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $descripcion = Descripcion::fromNullableString('test value');
        $this->assertInstanceOf(Descripcion::class, $descripcion);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $descripcion = Descripcion::fromNullableString(null);
        $this->assertNull($descripcion);
    }

}
