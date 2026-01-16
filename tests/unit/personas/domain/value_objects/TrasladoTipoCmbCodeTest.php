<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\TrasladoTipoCmbCode;
use Tests\myTest;

class TrasladoTipoCmbCodeTest extends myTest
{
    // TrasladoTipoCmbCode must be at most 4 characters
    public function test_create_valid_trasladoTipoCmbCode()
    {
        $trasladoTipoCmbCode = new TrasladoTipoCmbCode('test');
        $this->assertEquals('test', $trasladoTipoCmbCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TrasladoTipoCmbCode(str_repeat('a', 6000)); // Assuming max length validation
    }

    public function test_to_string_returns_trasladoTipoCmbCode_value()
    {
        $trasladoTipoCmbCode = new TrasladoTipoCmbCode('test');
        $this->assertEquals('test', (string)$trasladoTipoCmbCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $trasladoTipoCmbCode = TrasladoTipoCmbCode::fromNullableString('test');
        $this->assertInstanceOf(TrasladoTipoCmbCode::class, $trasladoTipoCmbCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $trasladoTipoCmbCode = TrasladoTipoCmbCode::fromNullableString(null);
        $this->assertNull($trasladoTipoCmbCode);
    }

}
