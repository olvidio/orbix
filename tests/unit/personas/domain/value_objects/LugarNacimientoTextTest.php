<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\LugarNacimientoText;
use Tests\myTest;

class LugarNacimientoTextTest extends myTest
{
    public function test_create_valid_lugarNacimientoText()
    {
        $lugarNacimientoText = new LugarNacimientoText('test value');
        $this->assertEquals('test value', $lugarNacimientoText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new LugarNacimientoText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_lugarNacimientoText_value()
    {
        $lugarNacimientoText = new LugarNacimientoText('test value');
        $this->assertEquals('test value', (string)$lugarNacimientoText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $lugarNacimientoText = LugarNacimientoText::fromNullableString('test value');
        $this->assertInstanceOf(LugarNacimientoText::class, $lugarNacimientoText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $lugarNacimientoText = LugarNacimientoText::fromNullableString(null);
        $this->assertNull($lugarNacimientoText);
    }

}
