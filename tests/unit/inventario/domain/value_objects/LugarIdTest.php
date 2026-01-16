<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\LugarId;
use Tests\myTest;

class LugarIdTest extends myTest
{
    public function test_create_valid_lugarId()
    {
        $lugarId = new LugarId(123);
        $this->assertEquals(123, $lugarId->value());
    }

    public function test_equals_returns_true_for_same_lugarId()
    {
        $lugarId1 = new LugarId(123);
        $lugarId2 = new LugarId(123);
        $this->assertTrue($lugarId1->equals($lugarId2));
    }

    public function test_equals_returns_false_for_different_lugarId()
    {
        $lugarId1 = new LugarId(123);
        $lugarId2 = new LugarId(456);
        $this->assertFalse($lugarId1->equals($lugarId2));
    }

    public function test_to_string_returns_lugarId_value()
    {
        $lugarId = new LugarId(123);
        $this->assertEquals(123, (string)$lugarId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $lugarId = LugarId::fromNullableInt(123);
        $this->assertInstanceOf(LugarId::class, $lugarId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $lugarId = LugarId::fromNullableInt(null);
        $this->assertNull($lugarId);
    }

}
