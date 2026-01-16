<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\ZonaId;
use Tests\myTest;

class ZonaIdTest extends myTest
{
    public function test_create_valid_zonaId()
    {
        $zonaId = new ZonaId(123);
        $this->assertEquals(123, $zonaId->value());
    }

    public function test_equals_returns_true_for_same_zonaId()
    {
        $zonaId1 = new ZonaId(123);
        $zonaId2 = new ZonaId(123);
        $this->assertTrue($zonaId1->equals($zonaId2));
    }

    public function test_equals_returns_false_for_different_zonaId()
    {
        $zonaId1 = new ZonaId(123);
        $zonaId2 = new ZonaId(456);
        $this->assertFalse($zonaId1->equals($zonaId2));
    }

    public function test_to_string_returns_zonaId_value()
    {
        $zonaId = new ZonaId(123);
        $this->assertEquals(123, (string)$zonaId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $zonaId = ZonaId::fromNullableInt(123);
        $this->assertInstanceOf(ZonaId::class, $zonaId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $zonaId = ZonaId::fromNullableInt(null);
        $this->assertNull($zonaId);
    }

}
