<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TipoLaborId;
use Tests\myTest;

class TipoLaborIdTest extends myTest
{
    public function test_create_valid_tipoLaborId()
    {
        $tipoLaborId = new TipoLaborId(123);
        $this->assertEquals(123, $tipoLaborId->value());
    }

    public function test_equals_returns_true_for_same_tipoLaborId()
    {
        $tipoLaborId1 = new TipoLaborId(123);
        $tipoLaborId2 = new TipoLaborId(123);
        $this->assertTrue($tipoLaborId1->equals($tipoLaborId2));
    }

    public function test_equals_returns_false_for_different_tipoLaborId()
    {
        $tipoLaborId1 = new TipoLaborId(123);
        $tipoLaborId2 = new TipoLaborId(456);
        $this->assertFalse($tipoLaborId1->equals($tipoLaborId2));
    }

    public function test_to_string_returns_tipoLaborId_value()
    {
        $tipoLaborId = new TipoLaborId(123);
        $this->assertEquals(123, (string)$tipoLaborId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tipoLaborId = TipoLaborId::fromNullableInt(123);
        $this->assertInstanceOf(TipoLaborId::class, $tipoLaborId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tipoLaborId = TipoLaborId::fromNullableInt(null);
        $this->assertNull($tipoLaborId);
    }

}
