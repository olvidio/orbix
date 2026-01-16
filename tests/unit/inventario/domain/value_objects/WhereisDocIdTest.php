<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\WhereisDocId;
use Tests\myTest;

class WhereisDocIdTest extends myTest
{
    public function test_create_valid_whereisDocId()
    {
        $whereisDocId = new WhereisDocId(123);
        $this->assertEquals(123, $whereisDocId->value());
    }

    public function test_equals_returns_true_for_same_whereisDocId()
    {
        $whereisDocId1 = new WhereisDocId(123);
        $whereisDocId2 = new WhereisDocId(123);
        $this->assertTrue($whereisDocId1->equals($whereisDocId2));
    }

    public function test_equals_returns_false_for_different_whereisDocId()
    {
        $whereisDocId1 = new WhereisDocId(123);
        $whereisDocId2 = new WhereisDocId(456);
        $this->assertFalse($whereisDocId1->equals($whereisDocId2));
    }

    public function test_to_string_returns_whereisDocId_value()
    {
        $whereisDocId = new WhereisDocId(123);
        $this->assertEquals(123, (string)$whereisDocId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $whereisDocId = WhereisDocId::fromNullableInt(123);
        $this->assertInstanceOf(WhereisDocId::class, $whereisDocId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $whereisDocId = WhereisDocId::fromNullableInt(null);
        $this->assertNull($whereisDocId);
    }

}
