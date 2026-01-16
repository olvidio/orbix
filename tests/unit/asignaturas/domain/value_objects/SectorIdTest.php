<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\SectorId;
use Tests\myTest;

class SectorIdTest extends myTest
{
    public function test_create_valid_sectorId()
    {
        $sectorId = new SectorId(123);
        $this->assertEquals(123, $sectorId->value());
    }

    public function test_equals_returns_true_for_same_sectorId()
    {
        $sectorId1 = new SectorId(123);
        $sectorId2 = new SectorId(123);
        $this->assertTrue($sectorId1->equals($sectorId2));
    }

    public function test_equals_returns_false_for_different_sectorId()
    {
        $sectorId1 = new SectorId(123);
        $sectorId2 = new SectorId(456);
        $this->assertFalse($sectorId1->equals($sectorId2));
    }

    public function test_to_string_returns_sectorId_value()
    {
        $sectorId = new SectorId(123);
        $this->assertEquals(123, (string)$sectorId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $sectorId = SectorId::fromNullableInt(123);
        $this->assertInstanceOf(SectorId::class, $sectorId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $sectorId = SectorId::fromNullableInt(null);
        $this->assertNull($sectorId);
    }

}
