<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TarifaCantidad;
use Tests\myTest;

class TarifaCantidadTest extends myTest
{
    public function test_create_valid_tarifaCantidad()
    {
        $tarifaCantidad = new TarifaCantidad(5.5);
        $this->assertEquals(5.5, $tarifaCantidad->value());
    }

    public function test_equals_returns_true_for_same_tarifaCantidad()
    {
        $tarifaCantidad1 = new TarifaCantidad(5.5);
        $tarifaCantidad2 = new TarifaCantidad(5.5);
        $this->assertTrue($tarifaCantidad1->equals($tarifaCantidad2));
    }

    public function test_equals_returns_false_for_different_tarifaCantidad()
    {
        $tarifaCantidad1 = new TarifaCantidad(5.5);
        $tarifaCantidad2 = new TarifaCantidad(9.99);
        $this->assertFalse($tarifaCantidad1->equals($tarifaCantidad2));
    }

    public function test_to_string_returns_tarifaCantidad_value()
    {
        $tarifaCantidad = new TarifaCantidad(5.5);
        $this->assertEquals(5.5, (string)$tarifaCantidad);
    }

    public function test_fromNullableFloat_returns_instance_for_valid_value()
    {
        $tarifaCantidad = TarifaCantidad::fromNullableFloat(5.5);
        $this->assertInstanceOf(TarifaCantidad::class, $tarifaCantidad);
    }

    public function test_fromNullableFloat_returns_null_for_null_value()
    {
        $tarifaCantidad = TarifaCantidad::fromNullableFloat(null);
        $this->assertNull($tarifaCantidad);
    }

}
