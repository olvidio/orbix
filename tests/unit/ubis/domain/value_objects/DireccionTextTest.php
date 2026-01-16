<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DireccionText;
use Tests\myTest;

class DireccionTextTest extends myTest
{
    public function test_create_valid_direccionText()
    {
        $direccionText = new DireccionText('test value');
        $this->assertEquals('test value', $direccionText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DireccionText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_direccionText_value()
    {
        $direccionText = new DireccionText('test value');
        $this->assertEquals('test value', (string)$direccionText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $direccionText = DireccionText::fromNullableString('test value');
        $this->assertInstanceOf(DireccionText::class, $direccionText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $direccionText = DireccionText::fromNullableString(null);
        $this->assertNull($direccionText);
    }

}
