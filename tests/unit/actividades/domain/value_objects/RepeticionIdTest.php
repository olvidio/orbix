<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\RepeticionId;
use Tests\myTest;

class RepeticionIdTest extends myTest
{
    public function test_create_valid_repeticionId()
    {
        $repeticionId = new RepeticionId(123);
        $this->assertEquals(123, $repeticionId->value());
    }

    public function test_equals_returns_true_for_same_repeticionId()
    {
        $repeticionId1 = new RepeticionId(123);
        $repeticionId2 = new RepeticionId(123);
        $this->assertTrue($repeticionId1->equals($repeticionId2));
    }

    public function test_equals_returns_false_for_different_repeticionId()
    {
        $repeticionId1 = new RepeticionId(123);
        $repeticionId2 = new RepeticionId(456);
        $this->assertFalse($repeticionId1->equals($repeticionId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $repeticionId = RepeticionId::fromNullableInt(123);
        $this->assertInstanceOf(RepeticionId::class, $repeticionId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $repeticionId = RepeticionId::fromNullableInt(null);
        $this->assertNull($repeticionId);
    }

}
