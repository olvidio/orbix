<?php

namespace Tests\unit\utils_database\domain\value_objects;

use src\utils_database\domain\value_objects\MapIdDl;
use Tests\myTest;

class MapIdDlTest extends myTest
{
    public function test_create_valid_mapIdDl()
    {
        $mapIdDl = new MapIdDl(123);
        $this->assertEquals(123, $mapIdDl->value());
    }

    public function test_equals_returns_true_for_same_mapIdDl()
    {
        $mapIdDl1 = new MapIdDl(123);
        $mapIdDl2 = new MapIdDl(123);
        $this->assertTrue($mapIdDl1->equals($mapIdDl2));
    }

    public function test_equals_returns_false_for_different_mapIdDl()
    {
        $mapIdDl1 = new MapIdDl(123);
        $mapIdDl2 = new MapIdDl(456);
        $this->assertFalse($mapIdDl1->equals($mapIdDl2));
    }

    public function test_to_string_returns_mapIdDl_value()
    {
        $mapIdDl = new MapIdDl(123);
        $this->assertEquals(123, (string)$mapIdDl);
    }

}
