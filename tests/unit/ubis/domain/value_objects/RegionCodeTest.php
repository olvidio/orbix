<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\RegionCode;
use Tests\myTest;

class RegionCodeTest extends myTest
{
    // RegionCode must be at most 6 characters
    public function test_create_valid_regionCode()
    {
        $regionCode = new RegionCode('test');
        $this->assertEquals('test', $regionCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RegionCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_regionCode()
    {
        $regionCode1 = new RegionCode('test');
        $regionCode2 = new RegionCode('test');
        $this->assertTrue($regionCode1->equals($regionCode2));
    }

    public function test_equals_returns_false_for_different_regionCode()
    {
        $regionCode1 = new RegionCode('test');
        $regionCode2 = new RegionCode('alter');
        $this->assertFalse($regionCode1->equals($regionCode2));
    }

    public function test_to_string_returns_regionCode_value()
    {
        $regionCode = new RegionCode('test');
        $this->assertEquals('test', (string)$regionCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $regionCode = RegionCode::fromNullableString('test');
        $this->assertInstanceOf(RegionCode::class, $regionCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $regionCode = RegionCode::fromNullableString(null);
        $this->assertNull($regionCode);
    }

}
