<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DescTelecoOrder;
use Tests\myTest;

class DescTelecoOrderTest extends myTest
{
    // DescTelecoOrder must be at most 2 digits (0-99)
    public function test_create_valid_descTelecoOrder()
    {
        $descTelecoOrder = new DescTelecoOrder(10);
        $this->assertEquals(10, $descTelecoOrder->value());
    }

    public function test_equals_returns_true_for_same_descTelecoOrder()
    {
        $descTelecoOrder1 = new DescTelecoOrder(10);
        $descTelecoOrder2 = new DescTelecoOrder(10);
        $this->assertTrue($descTelecoOrder1->equals($descTelecoOrder2));
    }

    public function test_equals_returns_false_for_different_descTelecoOrder()
    {
        $descTelecoOrder1 = new DescTelecoOrder(10);
        $descTelecoOrder2 = new DescTelecoOrder(20);
        $this->assertFalse($descTelecoOrder1->equals($descTelecoOrder2));
    }

    public function test_to_string_returns_descTelecoOrder_value()
    {
        $descTelecoOrder = new DescTelecoOrder(10);
        $this->assertEquals(10, (string)$descTelecoOrder);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $descTelecoOrder = DescTelecoOrder::fromNullableInt(10);
        $this->assertInstanceOf(DescTelecoOrder::class, $descTelecoOrder);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $descTelecoOrder = DescTelecoOrder::fromNullableInt(null);
        $this->assertNull($descTelecoOrder);
    }

}
