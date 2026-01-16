<?php

namespace Tests\unit\actividadcargos\domain\value_objects;

use src\actividadcargos\domain\value_objects\OrdenCargo;
use Tests\myTest;

class OrdenCargoTest extends myTest
{
    public function test_create_valid_ordenCargo()
    {
        $ordenCargo = new OrdenCargo(123);
        $this->assertEquals(123, $ordenCargo->value());
    }

    public function test_equals_returns_true_for_same_ordenCargo()
    {
        $ordenCargo1 = new OrdenCargo(123);
        $ordenCargo2 = new OrdenCargo(123);
        $this->assertTrue($ordenCargo1->equals($ordenCargo2));
    }

    public function test_equals_returns_false_for_different_ordenCargo()
    {
        $ordenCargo1 = new OrdenCargo(123);
        $ordenCargo2 = new OrdenCargo(456);
        $this->assertFalse($ordenCargo1->equals($ordenCargo2));
    }

    public function test_to_string_returns_ordenCargo_value()
    {
        $ordenCargo = new OrdenCargo(123);
        $this->assertEquals(123, (string)$ordenCargo);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $ordenCargo = OrdenCargo::fromNullableInt(123);
        $this->assertInstanceOf(OrdenCargo::class, $ordenCargo);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $ordenCargo = OrdenCargo::fromNullableInt(null);
        $this->assertNull($ordenCargo);
    }

}
