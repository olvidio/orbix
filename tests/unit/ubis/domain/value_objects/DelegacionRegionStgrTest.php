<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DelegacionRegionStgr;
use Tests\myTest;

class DelegacionRegionStgrTest extends myTest
{
    // 'DelegacionRegionStgr must be at most 5 characters')
    public function test_create_valid_delegacionRegionStgr()
    {
        $delegacionRegionStgr = new DelegacionRegionStgr('Galbel');
        $this->assertEquals('Galbel', $delegacionRegionStgr->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DelegacionRegionStgr(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_delegacionRegionStgr()
    {
        $delegacionRegionStgr1 = new DelegacionRegionStgr('Galbel');
        $delegacionRegionStgr2 = new DelegacionRegionStgr('Galbel');
        $this->assertTrue($delegacionRegionStgr1->equals($delegacionRegionStgr2));
    }

    public function test_equals_returns_false_for_different_delegacionRegionStgr()
    {
        $delegacionRegionStgr1 = new DelegacionRegionStgr('Galbel');
        $delegacionRegionStgr2 = new DelegacionRegionStgr('H');
        $this->assertFalse($delegacionRegionStgr1->equals($delegacionRegionStgr2));
    }

    public function test_to_string_returns_delegacionRegionStgr_value()
    {
        $delegacionRegionStgr = new DelegacionRegionStgr('Galbel');
        $this->assertEquals('Galbel', (string)$delegacionRegionStgr);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $delegacionRegionStgr = DelegacionRegionStgr::fromNullableString('Galbel');
        $this->assertInstanceOf(DelegacionRegionStgr::class, $delegacionRegionStgr);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $delegacionRegionStgr = DelegacionRegionStgr::fromNullableString(null);
        $this->assertNull($delegacionRegionStgr);
    }

}
