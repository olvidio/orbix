<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\LatitudDecimal;
use Tests\myTest;

class LatitudDecimalTest extends myTest
{
    public function test_create_valid_latitudDecimal()
    {
        $latitudDecimal = new LatitudDecimal(5.5);
        $this->assertEquals(5.5, $latitudDecimal->value());
    }

    public function test_fromNullableFloat_returns_instance_for_valid_value()
    {
        $latitudDecimal = LatitudDecimal::fromNullableFloat(5.5);
        $this->assertInstanceOf(LatitudDecimal::class, $latitudDecimal);
    }

    public function test_fromNullableFloat_returns_null_for_null_value()
    {
        $latitudDecimal = LatitudDecimal::fromNullableFloat(null);
        $this->assertNull($latitudDecimal);
    }

}
