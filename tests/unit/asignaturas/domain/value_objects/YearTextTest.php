<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\YearText;
use Tests\myTest;

class YearTextTest extends myTest
{
    public function test_create_valid_yearText()
    {
        $yearText = new YearText('4');
        $this->assertEquals('4', $yearText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new YearText(str_repeat('a', 2)); // Assuming max length validation
    }

    public function test_invalid_length_throws_exception2()
    {
        $this->expectException(\InvalidArgumentException::class);
        new YearText('a'); // only numeric
    }

    public function test_to_string_returns_yearText_value()
    {
        $yearText = new YearText('4');
        $this->assertEquals('4', (string)$yearText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $yearText = YearText::fromNullableString('4');
        $this->assertInstanceOf(YearText::class, $yearText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $yearText = YearText::fromNullableString(null);
        $this->assertNull($yearText);
    }

}
