<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\CodigoPostalText;
use Tests\myTest;

class CodigoPostalTextTest extends myTest
{
    public function test_create_valid_codigoPostalText()
    {
        $codigoPostalText = new CodigoPostalText('test value');
        $this->assertEquals('test value', $codigoPostalText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CodigoPostalText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_codigoPostalText_value()
    {
        $codigoPostalText = new CodigoPostalText('test value');
        $this->assertEquals('test value', (string)$codigoPostalText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $codigoPostalText = CodigoPostalText::fromNullableString('test value');
        $this->assertInstanceOf(CodigoPostalText::class, $codigoPostalText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $codigoPostalText = CodigoPostalText::fromNullableString(null);
        $this->assertNull($codigoPostalText);
    }

}
