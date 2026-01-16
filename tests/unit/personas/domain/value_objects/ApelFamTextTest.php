<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\ApelFamText;
use Tests\myTest;

class ApelFamTextTest extends myTest
{
    public function test_create_valid_apelFamText()
    {
        $apelFamText = new ApelFamText('test value');
        $this->assertEquals('test value', $apelFamText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ApelFamText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_apelFamText_value()
    {
        $apelFamText = new ApelFamText('test value');
        $this->assertEquals('test value', (string)$apelFamText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $apelFamText = ApelFamText::fromNullableString('test value');
        $this->assertInstanceOf(ApelFamText::class, $apelFamText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $apelFamText = ApelFamText::fromNullableString(null);
        $this->assertNull($apelFamText);
    }

}
