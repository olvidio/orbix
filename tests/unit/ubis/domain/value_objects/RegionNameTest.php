<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\RegionName;
use Tests\myTest;

class RegionNameTest extends myTest
{
    public function test_create_valid_regionName()
    {
        $regionName = new RegionName('test value');
        $this->assertEquals('test value', $regionName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RegionName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_regionName()
    {
        $regionName1 = new RegionName('test value');
        $regionName2 = new RegionName('test value');
        $this->assertTrue($regionName1->equals($regionName2));
    }

    public function test_equals_returns_false_for_different_regionName()
    {
        $regionName1 = new RegionName('test value');
        $regionName2 = new RegionName('alternative value');
        $this->assertFalse($regionName1->equals($regionName2));
    }

    public function test_to_string_returns_regionName_value()
    {
        $regionName = new RegionName('test value');
        $this->assertEquals('test value', (string)$regionName);
    }

}
