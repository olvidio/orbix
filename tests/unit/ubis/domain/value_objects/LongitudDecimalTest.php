<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\LongitudDecimal;
use Tests\myTest;

class LongitudDecimalTest extends myTest
{
    public function test_create_valid_longitudDecimal()
    {
        $longitudDecimal = new LongitudDecimal(5.5);
        $this->assertEquals(5.5, $longitudDecimal->value());
    }

    public function test_fromNullableFloat_returns_instance_for_valid_value()
    {
        $longitudDecimal = LongitudDecimal::fromNullableFloat(5.5);
        $this->assertInstanceOf(LongitudDecimal::class, $longitudDecimal);
    }

    public function test_fromNullableFloat_returns_null_for_null_value()
    {
        $longitudDecimal = LongitudDecimal::fromNullableFloat(null);
        $this->assertNull($longitudDecimal);
    }

}
