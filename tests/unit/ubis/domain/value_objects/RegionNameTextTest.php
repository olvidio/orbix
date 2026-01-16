<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\RegionNameText;
use Tests\myTest;

class RegionNameTextTest extends myTest
{
    public function test_create_valid_regionNameText()
    {
        $regionNameText = new RegionNameText('test value');
        $this->assertEquals('test value', $regionNameText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RegionNameText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_regionNameText()
    {
        $regionNameText1 = new RegionNameText('test value');
        $regionNameText2 = new RegionNameText('test value');
        $this->assertTrue($regionNameText1->equals($regionNameText2));
    }

    public function test_equals_returns_false_for_different_regionNameText()
    {
        $regionNameText1 = new RegionNameText('test value');
        $regionNameText2 = new RegionNameText('alternative value');
        $this->assertFalse($regionNameText1->equals($regionNameText2));
    }

    public function test_to_string_returns_regionNameText_value()
    {
        $regionNameText = new RegionNameText('test value');
        $this->assertEquals('test value', (string)$regionNameText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $regionNameText = RegionNameText::fromNullableString('test value');
        $this->assertInstanceOf(RegionNameText::class, $regionNameText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $regionNameText = RegionNameText::fromNullableString(null);
        $this->assertNull($regionNameText);
    }

}
