<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoPrioridad;
use Tests\myTest;

class EncargoPrioridadTest extends myTest
{
    public function test_create_valid_encargoPrioridad()
    {
        $encargoPrioridad = new EncargoPrioridad(123);
        $this->assertEquals(123, $encargoPrioridad->value());
    }

    public function test_equals_returns_true_for_same_encargoPrioridad()
    {
        $encargoPrioridad1 = new EncargoPrioridad(123);
        $encargoPrioridad2 = new EncargoPrioridad(123);
        $this->assertTrue($encargoPrioridad1->equals($encargoPrioridad2));
    }

    public function test_equals_returns_false_for_different_encargoPrioridad()
    {
        $encargoPrioridad1 = new EncargoPrioridad(123);
        $encargoPrioridad2 = new EncargoPrioridad(456);
        $this->assertFalse($encargoPrioridad1->equals($encargoPrioridad2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $encargoPrioridad = EncargoPrioridad::fromNullableInt(123);
        $this->assertInstanceOf(EncargoPrioridad::class, $encargoPrioridad);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $encargoPrioridad = EncargoPrioridad::fromNullableInt(null);
        $this->assertNull($encargoPrioridad);
    }

}
