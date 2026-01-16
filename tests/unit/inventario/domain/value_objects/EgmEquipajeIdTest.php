<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EgmEquipajeId;
use Tests\myTest;

class EgmEquipajeIdTest extends myTest
{
    public function test_create_valid_egmEquipajeId()
    {
        $egmEquipajeId = new EgmEquipajeId(123);
        $this->assertEquals(123, $egmEquipajeId->value());
    }

    public function test_equals_returns_true_for_same_egmEquipajeId()
    {
        $egmEquipajeId1 = new EgmEquipajeId(123);
        $egmEquipajeId2 = new EgmEquipajeId(123);
        $this->assertTrue($egmEquipajeId1->equals($egmEquipajeId2));
    }

    public function test_equals_returns_false_for_different_egmEquipajeId()
    {
        $egmEquipajeId1 = new EgmEquipajeId(123);
        $egmEquipajeId2 = new EgmEquipajeId(456);
        $this->assertFalse($egmEquipajeId1->equals($egmEquipajeId2));
    }

    public function test_to_string_returns_egmEquipajeId_value()
    {
        $egmEquipajeId = new EgmEquipajeId(123);
        $this->assertEquals(123, (string)$egmEquipajeId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $egmEquipajeId = EgmEquipajeId::fromNullableInt(123);
        $this->assertInstanceOf(EgmEquipajeId::class, $egmEquipajeId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $egmEquipajeId = EgmEquipajeId::fromNullableInt(null);
        $this->assertNull($egmEquipajeId);
    }

}
