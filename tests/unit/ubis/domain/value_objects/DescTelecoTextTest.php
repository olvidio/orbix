<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\int;
use Tests\myTest;

class DescTelecoTextTest extends myTest
{
    public function test_create_valid_descTelecoText()
    {
        $descTelecoText = new int('test value');
        $this->assertEquals('test value', $descTelecoText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new int(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_descTelecoText()
    {
        $descTelecoText1 = new int('test value');
        $descTelecoText2 = new int('test value');
        $this->assertTrue($descTelecoText1->equals($descTelecoText2));
    }

    public function test_equals_returns_false_for_different_descTelecoText()
    {
        $descTelecoText1 = new int('test value');
        $descTelecoText2 = new int('alternative value');
        $this->assertFalse($descTelecoText1->equals($descTelecoText2));
    }

    public function test_to_string_returns_descTelecoText_value()
    {
        $descTelecoText = new int('test value');
        $this->assertEquals('test value', (string)$descTelecoText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $descTelecoText = int::fromNullableString('test value');
        $this->assertInstanceOf(int::class, $descTelecoText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $descTelecoText = int::fromNullableString(null);
        $this->assertNull($descTelecoText);
    }

}
