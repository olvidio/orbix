<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\SectorName;
use Tests\myTest;

class SectorNameTest extends myTest
{
    public function test_create_valid_sectorName()
    {
        $sectorName = new SectorName('test value');
        $this->assertEquals('test value', $sectorName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new SectorName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_sectorName()
    {
        $sectorName1 = new SectorName('test value');
        $sectorName2 = new SectorName('test value');
        $this->assertTrue($sectorName1->equals($sectorName2));
    }

    public function test_equals_returns_false_for_different_sectorName()
    {
        $sectorName1 = new SectorName('test value');
        $sectorName2 = new SectorName('alternative value');
        $this->assertFalse($sectorName1->equals($sectorName2));
    }

    public function test_to_string_returns_sectorName_value()
    {
        $sectorName = new SectorName('test value');
        $this->assertEquals('test value', (string)$sectorName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $sectorName = SectorName::fromNullableString('test value');
        $this->assertInstanceOf(SectorName::class, $sectorName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $sectorName = SectorName::fromNullableString(null);
        $this->assertNull($sectorName);
    }

}
