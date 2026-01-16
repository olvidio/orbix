<?php

namespace Tests\unit\casas\domain\value_objects;

use src\casas\domain\value_objects\IngresoImporte;
use Tests\myTest;

class IngresoImporteTest extends myTest
{
    public function test_create_valid_ingresoImporte()
    {
        $ingresoImporte = new IngresoImporte(5.5);
        $this->assertEquals(5.5, $ingresoImporte->value());
    }

    public function test_equals_returns_true_for_same_ingresoImporte()
    {
        $ingresoImporte1 = new IngresoImporte(5.5);
        $ingresoImporte2 = new IngresoImporte(5.5);
        $this->assertTrue($ingresoImporte1->equals($ingresoImporte2));
    }

    public function test_equals_returns_false_for_different_ingresoImporte()
    {
        $ingresoImporte1 = new IngresoImporte(5.5);
        $ingresoImporte2 = new IngresoImporte(9.99);
        $this->assertFalse($ingresoImporte1->equals($ingresoImporte2));
    }

    public function test_fromNullableFloat_returns_instance_for_valid_value()
    {
        $ingresoImporte = IngresoImporte::fromNullableFloat(5.5);
        $this->assertInstanceOf(IngresoImporte::class, $ingresoImporte);
    }

    public function test_fromNullableFloat_returns_null_for_null_value()
    {
        $ingresoImporte = IngresoImporte::fromNullableFloat(null);
        $this->assertNull($ingresoImporte);
    }

}
