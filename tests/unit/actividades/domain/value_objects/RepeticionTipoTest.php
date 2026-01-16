<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\RepeticionTipo;
use Tests\myTest;

class RepeticionTipoTest extends myTest
{
    // OJO RepeticionTipo debe ser un dígito entre 0 y 3
    public function test_create_valid_repeticionTipo()
    {
        $repeticionTipo = new RepeticionTipo(1);
        $this->assertEquals(1, $repeticionTipo->value());
    }

    public function test_equals_returns_true_for_same_repeticionTipo()
    {
        $repeticionTipo1 = new RepeticionTipo(1);
        $repeticionTipo2 = new RepeticionTipo(1);
        $this->assertTrue($repeticionTipo1->equals($repeticionTipo2));
    }

    public function test_equals_returns_false_for_different_repeticionTipo()
    {
        $repeticionTipo1 = new RepeticionTipo(1);
        $repeticionTipo2 = new RepeticionTipo(2);
        $this->assertFalse($repeticionTipo1->equals($repeticionTipo2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $repeticionTipo = RepeticionTipo::fromNullableInt(1);
        $this->assertInstanceOf(RepeticionTipo::class, $repeticionTipo);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $repeticionTipo = RepeticionTipo::fromNullableInt(null);
        $this->assertNull($repeticionTipo);
    }

}
