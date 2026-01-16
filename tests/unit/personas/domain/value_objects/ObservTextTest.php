<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\ObservText;
use Tests\myTest;

class ObservTextTest extends myTest
{
    public function test_create_valid_observText()
    {
        $observText = new ObservText('test value');
        $this->assertEquals('test value', $observText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ObservText(str_repeat('a', 6000)); // Assuming max length validation
    }

    public function test_to_string_returns_observText_value()
    {
        $observText = new ObservText('test value');
        $this->assertEquals('test value', (string)$observText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observText = ObservText::fromNullableString('test value');
        $this->assertInstanceOf(ObservText::class, $observText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observText = ObservText::fromNullableString(null);
        $this->assertNull($observText);
    }

}
