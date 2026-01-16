<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\NumTelecoText;
use Tests\myTest;

class NumTelecoTextTest extends myTest
{
    public function test_create_valid_numTelecoText()
    {
        $numTelecoText = new NumTelecoText('test value');
        $this->assertEquals('test value', $numTelecoText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new NumTelecoText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_numTelecoText()
    {
        $numTelecoText1 = new NumTelecoText('test value');
        $numTelecoText2 = new NumTelecoText('test value');
        $this->assertTrue($numTelecoText1->equals($numTelecoText2));
    }

    public function test_equals_returns_false_for_different_numTelecoText()
    {
        $numTelecoText1 = new NumTelecoText('test value');
        $numTelecoText2 = new NumTelecoText('alternative value');
        $this->assertFalse($numTelecoText1->equals($numTelecoText2));
    }

    public function test_to_string_returns_numTelecoText_value()
    {
        $numTelecoText = new NumTelecoText('test value');
        $this->assertEquals('test value', (string)$numTelecoText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $numTelecoText = NumTelecoText::fromNullableString('test value');
        $this->assertInstanceOf(NumTelecoText::class, $numTelecoText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $numTelecoText = NumTelecoText::fromNullableString(null);
        $this->assertNull($numTelecoText);
    }

}
