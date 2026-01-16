<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Detalle;
use Tests\myTest;

class DetalleTest extends myTest
{
    public function test_create_valid_detalle()
    {
        $detalle = new Detalle('test value');
        $this->assertEquals('test value', $detalle->value());
    }

    public function test_to_string_returns_detalle_value()
    {
        $detalle = new Detalle('test value');
        $this->assertEquals('test value', (string)$detalle);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $detalle = Detalle::fromNullableString('test value');
        $this->assertInstanceOf(Detalle::class, $detalle);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $detalle = Detalle::fromNullableString(null);
        $this->assertNull($detalle);
    }

}
