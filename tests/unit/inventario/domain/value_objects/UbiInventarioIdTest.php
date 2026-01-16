<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\UbiInventarioId;
use Tests\myTest;

class UbiInventarioIdTest extends myTest
{
    public function test_create_valid_ubiInventarioId()
    {
        $ubiInventarioId = new UbiInventarioId(123);
        $this->assertEquals(123, $ubiInventarioId->value());
    }

    public function test_equals_returns_true_for_same_ubiInventarioId()
    {
        $ubiInventarioId1 = new UbiInventarioId(123);
        $ubiInventarioId2 = new UbiInventarioId(123);
        $this->assertTrue($ubiInventarioId1->equals($ubiInventarioId2));
    }

    public function test_equals_returns_false_for_different_ubiInventarioId()
    {
        $ubiInventarioId1 = new UbiInventarioId(123);
        $ubiInventarioId2 = new UbiInventarioId(456);
        $this->assertFalse($ubiInventarioId1->equals($ubiInventarioId2));
    }

    public function test_to_string_returns_ubiInventarioId_value()
    {
        $ubiInventarioId = new UbiInventarioId(123);
        $this->assertEquals(123, (string)$ubiInventarioId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $ubiInventarioId = UbiInventarioId::fromNullableInt(123);
        $this->assertInstanceOf(UbiInventarioId::class, $ubiInventarioId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $ubiInventarioId = UbiInventarioId::fromNullableInt(null);
        $this->assertNull($ubiInventarioId);
    }

}
