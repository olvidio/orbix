<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EgmLugarId;
use Tests\myTest;

class EgmLugarIdTest extends myTest
{
    public function test_create_valid_egmLugarId()
    {
        $egmLugarId = new EgmLugarId(123);
        $this->assertEquals(123, $egmLugarId->value());
    }

    public function test_equals_returns_true_for_same_egmLugarId()
    {
        $egmLugarId1 = new EgmLugarId(123);
        $egmLugarId2 = new EgmLugarId(123);
        $this->assertTrue($egmLugarId1->equals($egmLugarId2));
    }

    public function test_equals_returns_false_for_different_egmLugarId()
    {
        $egmLugarId1 = new EgmLugarId(123);
        $egmLugarId2 = new EgmLugarId(456);
        $this->assertFalse($egmLugarId1->equals($egmLugarId2));
    }

    public function test_to_string_returns_egmLugarId_value()
    {
        $egmLugarId = new EgmLugarId(123);
        $this->assertEquals(123, (string)$egmLugarId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $egmLugarId = EgmLugarId::fromNullableInt(123);
        $this->assertInstanceOf(EgmLugarId::class, $egmLugarId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $egmLugarId = EgmLugarId::fromNullableInt(null);
        $this->assertNull($egmLugarId);
    }

}
