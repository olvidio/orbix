<?php

namespace Tests\unit\utils_database\domain\value_objects;

use src\utils_database\domain\value_objects\MapIdResto;
use Tests\myTest;

class MapIdRestoTest extends myTest
{
    public function test_create_valid_mapIdResto()
    {
        $mapIdResto = new MapIdResto(123);
        $this->assertEquals(123, $mapIdResto->value());
    }

    public function test_equals_returns_true_for_same_mapIdResto()
    {
        $mapIdResto1 = new MapIdResto(123);
        $mapIdResto2 = new MapIdResto(123);
        $this->assertTrue($mapIdResto1->equals($mapIdResto2));
    }

    public function test_equals_returns_false_for_different_mapIdResto()
    {
        $mapIdResto1 = new MapIdResto(123);
        $mapIdResto2 = new MapIdResto(456);
        $this->assertFalse($mapIdResto1->equals($mapIdResto2));
    }

    public function test_to_string_returns_mapIdResto_value()
    {
        $mapIdResto = new MapIdResto(123);
        $this->assertEquals(123, (string)$mapIdResto);
    }

}
