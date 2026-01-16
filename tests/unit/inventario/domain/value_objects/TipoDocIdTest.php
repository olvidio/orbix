<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\TipoDocId;
use Tests\myTest;

class TipoDocIdTest extends myTest
{
    public function test_create_valid_tipoDocId()
    {
        $tipoDocId = new TipoDocId(123);
        $this->assertEquals(123, $tipoDocId->value());
    }

    public function test_equals_returns_true_for_same_tipoDocId()
    {
        $tipoDocId1 = new TipoDocId(123);
        $tipoDocId2 = new TipoDocId(123);
        $this->assertTrue($tipoDocId1->equals($tipoDocId2));
    }

    public function test_equals_returns_false_for_different_tipoDocId()
    {
        $tipoDocId1 = new TipoDocId(123);
        $tipoDocId2 = new TipoDocId(456);
        $this->assertFalse($tipoDocId1->equals($tipoDocId2));
    }

    public function test_to_string_returns_tipoDocId_value()
    {
        $tipoDocId = new TipoDocId(123);
        $this->assertEquals(123, (string)$tipoDocId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tipoDocId = TipoDocId::fromNullableInt(123);
        $this->assertInstanceOf(TipoDocId::class, $tipoDocId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tipoDocId = TipoDocId::fromNullableInt(null);
        $this->assertNull($tipoDocId);
    }

}
