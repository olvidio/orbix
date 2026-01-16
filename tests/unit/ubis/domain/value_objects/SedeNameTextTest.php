<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\SedeNameText;
use Tests\myTest;

class SedeNameTextTest extends myTest
{
    public function test_create_valid_sedeNameText()
    {
        $sedeNameText = new SedeNameText('test value');
        $this->assertEquals('test value', $sedeNameText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new SedeNameText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $sedeNameText = SedeNameText::fromNullableString('test value');
        $this->assertInstanceOf(SedeNameText::class, $sedeNameText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $sedeNameText = SedeNameText::fromNullableString(null);
        $this->assertNull($sedeNameText);
    }

}
