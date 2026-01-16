<?php

namespace Tests\unit\actividadtarifas\domain\value_objects;

use src\actividadtarifas\domain\value_objects\SerieId;
use Tests\myTest;

class SerieIdTest extends myTest
{
    public function test_create_valid_serieId()
    {
        $serieId = new SerieId(SerieId::GENERAL);
        $this->assertEquals(1, $serieId->value());
    }

    public function test_equals_returns_true_for_same_serieId()
    {
        $serieId1 = new SerieId(SerieId::GENERAL);
        $serieId2 = new SerieId(SerieId::GENERAL);
        $this->assertTrue($serieId1->equals($serieId2));
    }

    public function test_equals_returns_false_for_different_serieId()
    {
        $serieId1 = new SerieId(SerieId::GENERAL);
        $serieId2 = new SerieId(SerieId::ESTUDIANTE);
        $this->assertFalse($serieId1->equals($serieId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $serieId = SerieId::fromNullableInt(SerieId::GENERAL);
        $this->assertInstanceOf(SerieId::class, $serieId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $serieId = SerieId::fromNullableInt(null);
        $this->assertNull($serieId);
    }

}
