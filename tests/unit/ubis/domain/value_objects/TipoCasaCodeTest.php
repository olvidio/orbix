<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TipoCasaCode;
use Tests\myTest;

class TipoCasaCodeTest extends myTest
{
    // TipoCasaCode must be at most 8 characters
    public function test_create_valid_tipoCasaCode()
    {
        $tipoCasaCode = new TipoCasaCode('test');
        $this->assertEquals('test', $tipoCasaCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoCasaCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoCasaCode()
    {
        $tipoCasaCode1 = new TipoCasaCode('test');
        $tipoCasaCode2 = new TipoCasaCode('test');
        $this->assertTrue($tipoCasaCode1->equals($tipoCasaCode2));
    }

    public function test_equals_returns_false_for_different_tipoCasaCode()
    {
        $tipoCasaCode1 = new TipoCasaCode('test');
        $tipoCasaCode2 = new TipoCasaCode('alterna');
        $this->assertFalse($tipoCasaCode1->equals($tipoCasaCode2));
    }

    public function test_to_string_returns_tipoCasaCode_value()
    {
        $tipoCasaCode = new TipoCasaCode('test');
        $this->assertEquals('test', (string)$tipoCasaCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoCasaCode = TipoCasaCode::fromNullableString('test');
        $this->assertInstanceOf(TipoCasaCode::class, $tipoCasaCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoCasaCode = TipoCasaCode::fromNullableString(null);
        $this->assertNull($tipoCasaCode);
    }

}
