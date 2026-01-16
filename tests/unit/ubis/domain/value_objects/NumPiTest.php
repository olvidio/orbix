<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\NumPi;
use Tests\myTest;

class NumPiTest extends myTest
{
    public function test_create_valid_numPi()
    {
        $numPi = new NumPi(123);
        $this->assertEquals(123, $numPi->value());
    }

    public function test_equals_returns_true_for_same_numPi()
    {
        $numPi1 = new NumPi(123);
        $numPi2 = new NumPi(123);
        $this->assertTrue($numPi1->equals($numPi2));
    }

    public function test_equals_returns_false_for_different_numPi()
    {
        $numPi1 = new NumPi(123);
        $numPi2 = new NumPi(456);
        $this->assertFalse($numPi1->equals($numPi2));
    }

    public function test_to_string_returns_numPi_value()
    {
        $numPi = new NumPi(123);
        $this->assertEquals(123, (string)$numPi);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $numPi = NumPi::fromNullableInt(123);
        $this->assertInstanceOf(NumPi::class, $numPi);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $numPi = NumPi::fromNullableInt(null);
        $this->assertNull($numPi);
    }

}
