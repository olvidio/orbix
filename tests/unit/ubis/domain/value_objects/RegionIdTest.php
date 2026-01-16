<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\RegionId;
use Tests\myTest;

class RegionIdTest extends myTest
{
    public function test_create_valid_regionId()
    {
        $regionId = new RegionId(123);
        $this->assertEquals(123, $regionId->value());
    }

    public function test_equals_returns_true_for_same_regionId()
    {
        $regionId1 = new RegionId(123);
        $regionId2 = new RegionId(123);
        $this->assertTrue($regionId1->equals($regionId2));
    }

    public function test_equals_returns_false_for_different_regionId()
    {
        $regionId1 = new RegionId(123);
        $regionId2 = new RegionId(456);
        $this->assertFalse($regionId1->equals($regionId2));
    }

    public function test_to_string_returns_regionId_value()
    {
        $regionId = new RegionId(123);
        $this->assertEquals(123, (string)$regionId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $regionId = RegionId::fromNullableInt(123);
        $this->assertInstanceOf(RegionId::class, $regionId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $regionId = RegionId::fromNullableInt(null);
        $this->assertNull($regionId);
    }

}
