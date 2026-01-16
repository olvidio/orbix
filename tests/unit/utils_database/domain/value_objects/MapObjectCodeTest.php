<?php

namespace Tests\unit\utils_database\domain\value_objects;

use src\utils_database\domain\value_objects\MapObjectCode;
use Tests\myTest;

class MapObjectCodeTest extends myTest
{
    public function test_create_valid_mapObjectCode()
    {
        $mapObjectCode = new MapObjectCode('test value');
        $this->assertEquals('test value', $mapObjectCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new MapObjectCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_mapObjectCode()
    {
        $mapObjectCode1 = new MapObjectCode('test value');
        $mapObjectCode2 = new MapObjectCode('test value');
        $this->assertTrue($mapObjectCode1->equals($mapObjectCode2));
    }

    public function test_equals_returns_false_for_different_mapObjectCode()
    {
        $mapObjectCode1 = new MapObjectCode('test value');
        $mapObjectCode2 = new MapObjectCode('alternative value');
        $this->assertFalse($mapObjectCode1->equals($mapObjectCode2));
    }

    public function test_to_string_returns_mapObjectCode_value()
    {
        $mapObjectCode = new MapObjectCode('test value');
        $this->assertEquals('test value', (string)$mapObjectCode);
    }

}
