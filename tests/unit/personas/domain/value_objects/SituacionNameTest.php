<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\SituacionName;
use Tests\myTest;

class SituacionNameTest extends myTest
{
    public function test_create_valid_situacionName()
    {
        $situacionName = new SituacionName('test value');
        $this->assertEquals('test value', $situacionName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new SituacionName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_situacionName()
    {
        $situacionName1 = new SituacionName('test value');
        $situacionName2 = new SituacionName('test value');
        $this->assertTrue($situacionName1->equals($situacionName2));
    }

    public function test_equals_returns_false_for_different_situacionName()
    {
        $situacionName1 = new SituacionName('test value');
        $situacionName2 = new SituacionName('alternative value');
        $this->assertFalse($situacionName1->equals($situacionName2));
    }

    public function test_to_string_returns_situacionName_value()
    {
        $situacionName = new SituacionName('test value');
        $this->assertEquals('test value', (string)$situacionName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $situacionName = SituacionName::fromNullableString('test value');
        $this->assertInstanceOf(SituacionName::class, $situacionName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $situacionName = SituacionName::fromNullableString(null);
        $this->assertNull($situacionName);
    }

}
