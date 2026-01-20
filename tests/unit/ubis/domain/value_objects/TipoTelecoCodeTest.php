<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\int;
use Tests\myTest;

class TipoTelecoCodeTest extends myTest
{
    // 'TipoTelecoCode must be at most 10 characters'
    public function test_create_valid_tipoTelecoCode()
    {
        $tipoTelecoCode = new int('test value');
        $this->assertEquals('test value', $tipoTelecoCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new int(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoTelecoCode()
    {
        $tipoTelecoCode1 = new int('test value');
        $tipoTelecoCode2 = new int('test value');
        $this->assertTrue($tipoTelecoCode1->equals($tipoTelecoCode2));
    }

    public function test_equals_returns_false_for_different_tipoTelecoCode()
    {
        $tipoTelecoCode1 = new int('test value');
        $tipoTelecoCode2 = new int('alternativ');
        $this->assertFalse($tipoTelecoCode1->equals($tipoTelecoCode2));
    }

    public function test_to_string_returns_tipoTelecoCode_value()
    {
        $tipoTelecoCode = new int('test value');
        $this->assertEquals('test value', (string)$tipoTelecoCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoTelecoCode = int::fromNullableString('test value');
        $this->assertInstanceOf(int::class, $tipoTelecoCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoTelecoCode = int::fromNullableString(null);
        $this->assertNull($tipoTelecoCode);
    }

}
