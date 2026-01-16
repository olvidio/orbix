<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\NumHabitIndiv;
use Tests\myTest;

class NumHabitIndivTest extends myTest
{
    public function test_create_valid_numHabitIndiv()
    {
        $numHabitIndiv = new NumHabitIndiv(123);
        $this->assertEquals(123, $numHabitIndiv->value());
    }

    public function test_equals_returns_true_for_same_numHabitIndiv()
    {
        $numHabitIndiv1 = new NumHabitIndiv(123);
        $numHabitIndiv2 = new NumHabitIndiv(123);
        $this->assertTrue($numHabitIndiv1->equals($numHabitIndiv2));
    }

    public function test_equals_returns_false_for_different_numHabitIndiv()
    {
        $numHabitIndiv1 = new NumHabitIndiv(123);
        $numHabitIndiv2 = new NumHabitIndiv(456);
        $this->assertFalse($numHabitIndiv1->equals($numHabitIndiv2));
    }

    public function test_to_string_returns_numHabitIndiv_value()
    {
        $numHabitIndiv = new NumHabitIndiv(123);
        $this->assertEquals(123, (string)$numHabitIndiv);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $numHabitIndiv = NumHabitIndiv::fromNullableInt(123);
        $this->assertInstanceOf(NumHabitIndiv::class, $numHabitIndiv);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $numHabitIndiv = NumHabitIndiv::fromNullableInt(null);
        $this->assertNull($numHabitIndiv);
    }

}
