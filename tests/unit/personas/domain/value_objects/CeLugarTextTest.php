<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\CeLugarText;
use Tests\myTest;

class CeLugarTextTest extends myTest
{
    public function test_create_valid_ceLugarText()
    {
        $ceLugarText = new CeLugarText('test value');
        $this->assertEquals('test value', $ceLugarText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CeLugarText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_ceLugarText_value()
    {
        $ceLugarText = new CeLugarText('test value');
        $this->assertEquals('test value', (string)$ceLugarText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $ceLugarText = CeLugarText::fromNullableString('test value');
        $this->assertInstanceOf(CeLugarText::class, $ceLugarText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $ceLugarText = CeLugarText::fromNullableString(null);
        $this->assertNull($ceLugarText);
    }

}
