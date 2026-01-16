<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EquipajeId;
use Tests\myTest;

class EquipajeIdTest extends myTest
{
    public function test_create_valid_equipajeId()
    {
        $equipajeId = new EquipajeId(123);
        $this->assertEquals(123, $equipajeId->value());
    }

    public function test_equals_returns_true_for_same_equipajeId()
    {
        $equipajeId1 = new EquipajeId(123);
        $equipajeId2 = new EquipajeId(123);
        $this->assertTrue($equipajeId1->equals($equipajeId2));
    }

    public function test_equals_returns_false_for_different_equipajeId()
    {
        $equipajeId1 = new EquipajeId(123);
        $equipajeId2 = new EquipajeId(456);
        $this->assertFalse($equipajeId1->equals($equipajeId2));
    }

    public function test_to_string_returns_equipajeId_value()
    {
        $equipajeId = new EquipajeId(123);
        $this->assertEquals(123, (string)$equipajeId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $equipajeId = EquipajeId::fromNullableInt(123);
        $this->assertInstanceOf(EquipajeId::class, $equipajeId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $equipajeId = EquipajeId::fromNullableInt(null);
        $this->assertNull($equipajeId);
    }

}
