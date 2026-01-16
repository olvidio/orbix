<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\PlazasMin;
use Tests\myTest;

class PlazasMinTest extends myTest
{
    public function test_create_valid_plazasMin()
    {
        $plazasMin = new PlazasMin(123);
        $this->assertEquals(123, $plazasMin->value());
    }

    public function test_equals_returns_true_for_same_plazasMin()
    {
        $plazasMin1 = new PlazasMin(123);
        $plazasMin2 = new PlazasMin(123);
        $this->assertTrue($plazasMin1->equals($plazasMin2));
    }

    public function test_equals_returns_false_for_different_plazasMin()
    {
        $plazasMin1 = new PlazasMin(123);
        $plazasMin2 = new PlazasMin(456);
        $this->assertFalse($plazasMin1->equals($plazasMin2));
    }

    public function test_to_string_returns_plazasMin_value()
    {
        $plazasMin = new PlazasMin(123);
        $this->assertEquals(123, (string)$plazasMin);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $plazasMin = PlazasMin::fromNullableInt(123);
        $this->assertInstanceOf(PlazasMin::class, $plazasMin);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $plazasMin = PlazasMin::fromNullableInt(null);
        $this->assertNull($plazasMin);
    }

}
