<?php

namespace Tests\unit\ubiscamas\domain\value_objects;

use src\ubiscamas\domain\value_objects\NumeroCamas;
use Tests\myTest;

class NumeroCamasTest extends myTest
{
    public function test_create_valid_numeroCamas()
    {
        $num = new NumeroCamas(4);
        $this->assertEquals(4, $num->value());
    }

    public function test_to_string_returns_value()
    {
        $num = new NumeroCamas(4);
        $this->assertEquals('4', (string)$num);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $num = NumeroCamas::fromNullableInt(4);
        $this->assertInstanceOf(NumeroCamas::class, $num);
        $this->assertEquals(4, $num->value());
    }

    public function test_fromNullableInt_returns_null_for_null()
    {
        $this->assertNull(NumeroCamas::fromNullableInt(null));
    }
}
