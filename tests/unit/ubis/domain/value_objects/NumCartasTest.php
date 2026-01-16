<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\NumCartas;
use Tests\myTest;

class NumCartasTest extends myTest
{
    public function test_create_valid_numCartas()
    {
        $numCartas = new NumCartas(123);
        $this->assertEquals(123, $numCartas->value());
    }

    public function test_equals_returns_true_for_same_numCartas()
    {
        $numCartas1 = new NumCartas(123);
        $numCartas2 = new NumCartas(123);
        $this->assertTrue($numCartas1->equals($numCartas2));
    }

    public function test_equals_returns_false_for_different_numCartas()
    {
        $numCartas1 = new NumCartas(123);
        $numCartas2 = new NumCartas(456);
        $this->assertFalse($numCartas1->equals($numCartas2));
    }

    public function test_to_string_returns_numCartas_value()
    {
        $numCartas = new NumCartas(123);
        $this->assertEquals(123, (string)$numCartas);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $numCartas = NumCartas::fromNullableInt(123);
        $this->assertInstanceOf(NumCartas::class, $numCartas);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $numCartas = NumCartas::fromNullableInt(null);
        $this->assertNull($numCartas);
    }

}
