<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\PoblacionText;
use Tests\myTest;

class PoblacionTextTest extends myTest
{
    public function test_create_valid_poblacionText()
    {
        $poblacionText = new PoblacionText('test value');
        $this->assertEquals('test value', $poblacionText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PoblacionText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_poblacionText_value()
    {
        $poblacionText = new PoblacionText('test value');
        $this->assertEquals('test value', (string)$poblacionText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $poblacionText = PoblacionText::fromNullableString('test value');
        $this->assertInstanceOf(PoblacionText::class, $poblacionText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $poblacionText = PoblacionText::fromNullableString(null);
        $this->assertNull($poblacionText);
    }

}
