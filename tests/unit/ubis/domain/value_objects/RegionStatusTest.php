<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\RegionStatus;
use Tests\myTest;

class RegionStatusTest extends myTest
{
    public function test_create_valid_regionStatus()
    {
        $regionStatus = new RegionStatus(true);
        $this->assertEquals(true, $regionStatus->value());
    }

    public function test_equals_returns_true_for_same_regionStatus()
    {
        $regionStatus1 = new RegionStatus(true);
        $regionStatus2 = new RegionStatus(true);
        $this->assertTrue($regionStatus1->equals($regionStatus2));
    }

    public function test_equals_returns_false_for_different_regionStatus()
    {
        $regionStatus1 = new RegionStatus(true);
        $regionStatus2 = new RegionStatus(false);
        $this->assertFalse($regionStatus1->equals($regionStatus2));
    }

    public function test_to_string_returns_regionStatus_value()
    {
        $regionStatus = new RegionStatus(true);
        $this->assertEquals(true, (string)$regionStatus);
    }

}
