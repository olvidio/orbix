<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DescTelecoText;
use Tests\myTest;

class DescTelecoTextTest extends myTest
{
    public function test_create_valid_descTelecoText()
    {
        $descTelecoText = new DescTelecoText('test value');
        $this->assertEquals('test value', $descTelecoText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DescTelecoText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_descTelecoText()
    {
        $descTelecoText1 = new DescTelecoText('test value');
        $descTelecoText2 = new DescTelecoText('test value');
        $this->assertTrue($descTelecoText1->equals($descTelecoText2));
    }

    public function test_equals_returns_false_for_different_descTelecoText()
    {
        $descTelecoText1 = new DescTelecoText('test value');
        $descTelecoText2 = new DescTelecoText('alternative value');
        $this->assertFalse($descTelecoText1->equals($descTelecoText2));
    }

    public function test_to_string_returns_descTelecoText_value()
    {
        $descTelecoText = new DescTelecoText('test value');
        $this->assertEquals('test value', (string)$descTelecoText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $descTelecoText = DescTelecoText::fromNullableString('test value');
        $this->assertInstanceOf(DescTelecoText::class, $descTelecoText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $descTelecoText = DescTelecoText::fromNullableString(null);
        $this->assertNull($descTelecoText);
    }

}
