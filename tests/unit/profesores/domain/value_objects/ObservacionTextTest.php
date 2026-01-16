<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\ObservacionText;
use Tests\myTest;

class ObservacionTextTest extends myTest
{
    public function test_create_valid_observacionText()
    {
        $observacionText = new ObservacionText('test value');
        $this->assertEquals('test value', $observacionText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ObservacionText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_observacionText_value()
    {
        $observacionText = new ObservacionText('test value');
        $this->assertEquals('test value', (string)$observacionText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observacionText = ObservacionText::fromNullableString('test value');
        $this->assertInstanceOf(ObservacionText::class, $observacionText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observacionText = ObservacionText::fromNullableString(null);
        $this->assertNull($observacionText);
    }

}
