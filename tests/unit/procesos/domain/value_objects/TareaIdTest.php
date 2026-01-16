<?php

namespace Tests\unit\procesos\domain\value_objects;

use src\procesos\domain\value_objects\TareaId;
use Tests\myTest;

class TareaIdTest extends myTest
{
    public function test_create_valid_tareaId()
    {
        $tareaId = new TareaId(123);
        $this->assertEquals(123, $tareaId->value());
    }

    public function test_equals_returns_true_for_same_tareaId()
    {
        $tareaId1 = new TareaId(123);
        $tareaId2 = new TareaId(123);
        $this->assertTrue($tareaId1->equals($tareaId2));
    }

    public function test_equals_returns_false_for_different_tareaId()
    {
        $tareaId1 = new TareaId(123);
        $tareaId2 = new TareaId(456);
        $this->assertFalse($tareaId1->equals($tareaId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tareaId = TareaId::fromNullableInt(123);
        $this->assertInstanceOf(TareaId::class, $tareaId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tareaId = TareaId::fromNullableInt(null);
        $this->assertNull($tareaId);
    }

}
