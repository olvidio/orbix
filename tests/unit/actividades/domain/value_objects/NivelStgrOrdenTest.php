<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\NivelStgrOrden;
use Tests\myTest;

class NivelStgrOrdenTest extends myTest
{
    public function test_create_valid_nivelStgrOrden()
    {
        $nivelStgrOrden = new NivelStgrOrden(123);
        $this->assertEquals(123, $nivelStgrOrden->value());
    }

    public function test_equals_returns_true_for_same_nivelStgrOrden()
    {
        $nivelStgrOrden1 = new NivelStgrOrden(123);
        $nivelStgrOrden2 = new NivelStgrOrden(123);
        $this->assertTrue($nivelStgrOrden1->equals($nivelStgrOrden2));
    }

    public function test_equals_returns_false_for_different_nivelStgrOrden()
    {
        $nivelStgrOrden1 = new NivelStgrOrden(123);
        $nivelStgrOrden2 = new NivelStgrOrden(456);
        $this->assertFalse($nivelStgrOrden1->equals($nivelStgrOrden2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $nivelStgrOrden = NivelStgrOrden::fromNullableInt(123);
        $this->assertInstanceOf(NivelStgrOrden::class, $nivelStgrOrden);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $nivelStgrOrden = NivelStgrOrden::fromNullableInt(null);
        $this->assertNull($nivelStgrOrden);
    }

}
