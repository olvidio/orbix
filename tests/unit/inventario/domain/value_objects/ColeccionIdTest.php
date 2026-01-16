<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\ColeccionId;
use Tests\myTest;

class ColeccionIdTest extends myTest
{
    public function test_create_valid_coleccionId()
    {
        $coleccionId = new ColeccionId(123);
        $this->assertEquals(123, $coleccionId->value());
    }

    public function test_equals_returns_true_for_same_coleccionId()
    {
        $coleccionId1 = new ColeccionId(123);
        $coleccionId2 = new ColeccionId(123);
        $this->assertTrue($coleccionId1->equals($coleccionId2));
    }

    public function test_equals_returns_false_for_different_coleccionId()
    {
        $coleccionId1 = new ColeccionId(123);
        $coleccionId2 = new ColeccionId(456);
        $this->assertFalse($coleccionId1->equals($coleccionId2));
    }

    public function test_to_string_returns_coleccionId_value()
    {
        $coleccionId = new ColeccionId(123);
        $this->assertEquals(123, (string)$coleccionId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $coleccionId = ColeccionId::fromNullableInt(123);
        $this->assertInstanceOf(ColeccionId::class, $coleccionId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $coleccionId = ColeccionId::fromNullableInt(null);
        $this->assertNull($coleccionId);
    }

}
