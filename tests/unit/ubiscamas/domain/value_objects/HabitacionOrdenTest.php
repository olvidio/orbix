<?php

namespace Tests\unit\ubiscamas\domain\value_objects;

use src\ubiscamas\domain\value_objects\HabitacionOrden;
use Tests\myTest;

class HabitacionOrdenTest extends myTest
{
    public function test_create_valid_habitacionOrden()
    {
        $orden = new HabitacionOrden(3);
        $this->assertEquals(3, $orden->value());
    }

    public function test_to_string_returns_value()
    {
        $orden = new HabitacionOrden(3);
        $this->assertEquals('3', (string)$orden);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $orden = HabitacionOrden::fromNullableInt(3);
        $this->assertInstanceOf(HabitacionOrden::class, $orden);
        $this->assertEquals(3, $orden->value());
    }

    public function test_fromNullableInt_returns_null_for_null()
    {
        $this->assertNull(HabitacionOrden::fromNullableInt(null));
    }
}
