<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\NBuzon;
use Tests\myTest;

class NBuzonTest extends myTest
{
    public function test_create_valid_nBuzon()
    {
        $nBuzon = new NBuzon(123);
        $this->assertEquals(123, $nBuzon->value());
    }

    public function test_equals_returns_true_for_same_nBuzon()
    {
        $nBuzon1 = new NBuzon(123);
        $nBuzon2 = new NBuzon(123);
        $this->assertTrue($nBuzon1->equals($nBuzon2));
    }

    public function test_equals_returns_false_for_different_nBuzon()
    {
        $nBuzon1 = new NBuzon(123);
        $nBuzon2 = new NBuzon(456);
        $this->assertFalse($nBuzon1->equals($nBuzon2));
    }

    public function test_to_string_returns_nBuzon_value()
    {
        $nBuzon = new NBuzon(123);
        $this->assertEquals(123, (string)$nBuzon);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $nBuzon = NBuzon::fromNullableInt(123);
        $this->assertInstanceOf(NBuzon::class, $nBuzon);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $nBuzon = NBuzon::fromNullableInt(null);
        $this->assertNull($nBuzon);
    }

}
