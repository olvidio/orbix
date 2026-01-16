<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\TemporadaCode;
use Tests\myTest;

class TemporadaCodeTest extends myTest
{
    // OJO TemporadaCode debe ser de un solo carácter
    public function test_create_valid_temporadaCode()
    {
        $temporadaCode = new TemporadaCode('t');
        $this->assertEquals('t', $temporadaCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TemporadaCode(str_repeat('a', 3)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_temporadaCode()
    {
        $temporadaCode1 = new TemporadaCode('t');
        $temporadaCode2 = new TemporadaCode('t');
        $this->assertTrue($temporadaCode1->equals($temporadaCode2));
    }

    public function test_equals_returns_false_for_different_temporadaCode()
    {
        $temporadaCode1 = new TemporadaCode('t');
        $temporadaCode2 = new TemporadaCode('a');
        $this->assertFalse($temporadaCode1->equals($temporadaCode2));
    }

    public function test_to_string_returns_temporadaCode_value()
    {
        $temporadaCode = new TemporadaCode('t');
        $this->assertEquals('t', (string)$temporadaCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $temporadaCode = TemporadaCode::fromNullableString('t');
        $this->assertInstanceOf(TemporadaCode::class, $temporadaCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $temporadaCode = TemporadaCode::fromNullableString(null);
        $this->assertNull($temporadaCode);
    }

}
