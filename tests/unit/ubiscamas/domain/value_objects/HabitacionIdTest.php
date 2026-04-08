<?php

namespace Tests\unit\ubiscamas\domain\value_objects;

use src\ubiscamas\domain\value_objects\HabitacionId;
use Tests\myTest;

class HabitacionIdTest extends myTest
{
    public function test_create_valid_habitacionId()
    {
        $habitacionId = new HabitacionId('hab-001');
        $this->assertEquals('hab-001', $habitacionId->value());
    }

    public function test_empty_string_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new HabitacionId('');
    }

    public function test_equals_returns_true_for_same_value()
    {
        $id1 = new HabitacionId('hab-001');
        $id2 = new HabitacionId('hab-001');
        $this->assertTrue($id1->equals($id2));
    }

    public function test_equals_returns_false_for_different_value()
    {
        $id1 = new HabitacionId('hab-001');
        $id2 = new HabitacionId('hab-002');
        $this->assertFalse($id1->equals($id2));
    }

    public function test_to_string_returns_value()
    {
        $habitacionId = new HabitacionId('hab-001');
        $this->assertEquals('hab-001', (string)$habitacionId);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $id = HabitacionId::fromNullableString('hab-001');
        $this->assertInstanceOf(HabitacionId::class, $id);
        $this->assertEquals('hab-001', $id->value());
    }

    public function test_fromNullableString_returns_null_for_null()
    {
        $this->assertNull(HabitacionId::fromNullableString(null));
    }

    public function test_fromNullableString_returns_null_for_empty_string()
    {
        $this->assertNull(HabitacionId::fromNullableString(''));
    }
}
