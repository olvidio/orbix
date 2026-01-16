<?php

namespace Tests\unit\actividadtarifas\domain\value_objects;

use src\actividadtarifas\domain\value_objects\TarifaId;
use Tests\myTest;

class TarifaIdTest extends myTest
{
    public function test_create_valid_tarifaId()
    {
        $tarifaId = new TarifaId(123);
        $this->assertEquals(123, $tarifaId->value());
    }

    public function test_equals_returns_true_for_same_tarifaId()
    {
        $tarifaId1 = new TarifaId(123);
        $tarifaId2 = new TarifaId(123);
        $this->assertTrue($tarifaId1->equals($tarifaId2));
    }

    public function test_equals_returns_false_for_different_tarifaId()
    {
        $tarifaId1 = new TarifaId(123);
        $tarifaId2 = new TarifaId(456);
        $this->assertFalse($tarifaId1->equals($tarifaId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tarifaId = TarifaId::fromNullableInt(123);
        $this->assertInstanceOf(TarifaId::class, $tarifaId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tarifaId = TarifaId::fromNullableInt(null);
        $this->assertNull($tarifaId);
    }

}
