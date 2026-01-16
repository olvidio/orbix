<?php

namespace Tests\unit\casas\domain\value_objects;

use src\casas\domain\value_objects\UbiGastoCantidad;
use Tests\myTest;

class UbiGastoCantidadTest extends myTest
{
    public function test_create_valid_ubiGastoCantidad()
    {
        $ubiGastoCantidad = new UbiGastoCantidad(5.5);
        $this->assertEquals(5.5, $ubiGastoCantidad->value());
    }

    public function test_equals_returns_true_for_same_ubiGastoCantidad()
    {
        $ubiGastoCantidad1 = new UbiGastoCantidad(5.5);
        $ubiGastoCantidad2 = new UbiGastoCantidad(5.5);
        $this->assertTrue($ubiGastoCantidad1->equals($ubiGastoCantidad2));
    }

    public function test_equals_returns_false_for_different_ubiGastoCantidad()
    {
        $ubiGastoCantidad1 = new UbiGastoCantidad(5.5);
        $ubiGastoCantidad2 = new UbiGastoCantidad(9.99);
        $this->assertFalse($ubiGastoCantidad1->equals($ubiGastoCantidad2));
    }

    public function test_fromNullableFloat_returns_instance_for_valid_value()
    {
        $ubiGastoCantidad = UbiGastoCantidad::fromNullableFloat(5.5);
        $this->assertInstanceOf(UbiGastoCantidad::class, $ubiGastoCantidad);
    }

    public function test_fromNullableFloat_returns_null_for_null_value()
    {
        $ubiGastoCantidad = UbiGastoCantidad::fromNullableFloat(null);
        $this->assertNull($ubiGastoCantidad);
    }

}
