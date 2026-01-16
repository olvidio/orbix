<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\SituacionCode;
use Tests\myTest;

class SituacionCodeTest extends myTest
{
    // SituacionCode must be exactly 1 character in upppercase
    public function test_create_valid_situacionCode()
    {
        $situacionCode = new SituacionCode('A');
        $this->assertEquals('A', $situacionCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new SituacionCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_situacionCode()
    {
        $situacionCode1 = new SituacionCode('A');
        $situacionCode2 = new SituacionCode('A');
        $this->assertTrue($situacionCode1->equals($situacionCode2));
    }

    public function test_equals_returns_false_for_different_situacionCode()
    {
        $situacionCode1 = new SituacionCode('A');
        $situacionCode2 = new SituacionCode('B');
        $this->assertFalse($situacionCode1->equals($situacionCode2));
    }

    public function test_to_string_returns_situacionCode_value()
    {
        $situacionCode = new SituacionCode('A');
        $this->assertEquals('A', (string)$situacionCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $situacionCode = SituacionCode::fromNullableString('A');
        $this->assertInstanceOf(SituacionCode::class, $situacionCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $situacionCode = SituacionCode::fromNullableString(null);
        $this->assertNull($situacionCode);
    }

}
