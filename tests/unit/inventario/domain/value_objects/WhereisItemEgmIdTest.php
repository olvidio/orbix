<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\WhereisItemEgmId;
use Tests\myTest;

class WhereisItemEgmIdTest extends myTest
{
    public function test_create_valid_whereisItemEgmId()
    {
        $whereisItemEgmId = new WhereisItemEgmId(123);
        $this->assertEquals(123, $whereisItemEgmId->value());
    }

    public function test_equals_returns_true_for_same_whereisItemEgmId()
    {
        $whereisItemEgmId1 = new WhereisItemEgmId(123);
        $whereisItemEgmId2 = new WhereisItemEgmId(123);
        $this->assertTrue($whereisItemEgmId1->equals($whereisItemEgmId2));
    }

    public function test_equals_returns_false_for_different_whereisItemEgmId()
    {
        $whereisItemEgmId1 = new WhereisItemEgmId(123);
        $whereisItemEgmId2 = new WhereisItemEgmId(456);
        $this->assertFalse($whereisItemEgmId1->equals($whereisItemEgmId2));
    }

    public function test_to_string_returns_whereisItemEgmId_value()
    {
        $whereisItemEgmId = new WhereisItemEgmId(123);
        $this->assertEquals(123, (string)$whereisItemEgmId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $whereisItemEgmId = WhereisItemEgmId::fromNullableInt(123);
        $this->assertInstanceOf(WhereisItemEgmId::class, $whereisItemEgmId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $whereisItemEgmId = WhereisItemEgmId::fromNullableInt(null);
        $this->assertNull($whereisItemEgmId);
    }

}
