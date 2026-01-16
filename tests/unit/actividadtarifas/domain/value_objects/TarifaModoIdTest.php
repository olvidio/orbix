<?php

namespace Tests\unit\actividadtarifas\domain\value_objects;

use src\actividadtarifas\domain\value_objects\TarifaModoId;
use Tests\myTest;

class TarifaModoIdTest extends myTest
{
    public function test_create_valid_tarifaModoId()
    {
        $tarifaModoId = new TarifaModoId(TarifaModoId::POR_DIA);
        $this->assertEquals(0, $tarifaModoId->value());
    }

    public function test_equals_returns_true_for_same_tarifaModoId()
    {
        $tarifaModoId1 = new TarifaModoId(TarifaModoId::POR_DIA);
        $tarifaModoId2 = new TarifaModoId(TarifaModoId::POR_DIA);
        $this->assertTrue($tarifaModoId1->equals($tarifaModoId2));
    }

    public function test_equals_returns_false_for_different_tarifaModoId()
    {
        $tarifaModoId1 = new TarifaModoId(TarifaModoId::POR_DIA);
        $tarifaModoId2 = new TarifaModoId(TarifaModoId::TOTAL);
        $this->assertFalse($tarifaModoId1->equals($tarifaModoId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tarifaModoId = TarifaModoId::fromNullableInt(TarifaModoId::POR_DIA);
        $this->assertInstanceOf(TarifaModoId::class, $tarifaModoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tarifaModoId = TarifaModoId::fromNullableInt(null);
        $this->assertNull($tarifaModoId);
    }

}
