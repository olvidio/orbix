<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\ProvinciaText;
use Tests\myTest;

class ProvinciaTextTest extends myTest
{
    public function test_create_valid_provinciaText()
    {
        $provinciaText = new ProvinciaText('test value');
        $this->assertEquals('test value', $provinciaText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProvinciaText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $provinciaText = ProvinciaText::fromNullableString('test value');
        $this->assertInstanceOf(ProvinciaText::class, $provinciaText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $provinciaText = ProvinciaText::fromNullableString(null);
        $this->assertNull($provinciaText);
    }

}
