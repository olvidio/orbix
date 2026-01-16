<?php

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\Dinero;
use Tests\myTest;

class DineroTest extends myTest
{
    public function test_create_valid_dinero()
    {
        $dinero = new Dinero('123.50');
        $this->assertEquals('123.50', $dinero->asFloat());
    }

    public function test_equals_returns_true_for_same_dinero()
    {
        $dinero1 = new Dinero('123.50');
        $dinero2 = new Dinero('123.50');
        $this->assertTrue($dinero1->equals($dinero2));
    }

    public function test_equals_returns_false_for_different_dinero()
    {
        $dinero1 = new Dinero('123.50');
        $dinero2 = new Dinero('56.78');
        $this->assertFalse($dinero1->equals($dinero2));
    }

    public function test_to_string_returns_dinero_value()
    {
        $dinero = new Dinero('123.50');
        $this->assertEquals('123.50', (string)$dinero);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $dinero = Dinero::fromNullableFloat('123.50');
        $this->assertInstanceOf(Dinero::class, $dinero);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $dinero = Dinero::fromNullableFloat(null);
        $this->assertNull($dinero);
    }

}
