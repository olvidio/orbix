<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DelegacionId;
use Tests\myTest;

class DelegacionIdTest extends myTest
{
    public function test_create_valid_delegacionId()
    {
        $delegacionId = new DelegacionId(123);
        $this->assertEquals(123, $delegacionId->value());
    }

    public function test_equals_returns_true_for_same_delegacionId()
    {
        $delegacionId1 = new DelegacionId(123);
        $delegacionId2 = new DelegacionId(123);
        $this->assertTrue($delegacionId1->equals($delegacionId2));
    }

    public function test_equals_returns_false_for_different_delegacionId()
    {
        $delegacionId1 = new DelegacionId(123);
        $delegacionId2 = new DelegacionId(456);
        $this->assertFalse($delegacionId1->equals($delegacionId2));
    }

    public function test_to_string_returns_delegacionId_value()
    {
        $delegacionId = new DelegacionId(123);
        $this->assertEquals(123, (string)$delegacionId);
    }

}
