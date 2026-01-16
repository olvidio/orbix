<?php

namespace Tests\unit\actividadplazas\domain\value_objects;

use src\actividadplazas\domain\value_objects\PlazaId;
use Tests\myTest;

class PlazaIdTest extends myTest
{
    public function test_create_valid_plazaId()
    {
        $plazaId = new PlazaId(PlazaId::PEDIDA);
        $this->assertEquals(1, $plazaId->value());
    }

    public function test_invalid_plazaId_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PlazaId(999);
    }

    public function test_equals_returns_true_for_same_plazaId()
    {
        $plazaId1 = new PlazaId(PlazaId::PEDIDA);
        $plazaId2 = new PlazaId(PlazaId::PEDIDA);
        $this->assertTrue($plazaId1->equals($plazaId2));
    }

    public function test_equals_returns_false_for_different_plazaId()
    {
        $plazaId1 = new PlazaId(PlazaId::PEDIDA);
        $plazaId2 = new PlazaId(PlazaId::EN_ESPERA);
        $this->assertFalse($plazaId1->equals($plazaId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $plazaId = PlazaId::fromNullableInt(PlazaId::PEDIDA);
        $this->assertInstanceOf(PlazaId::class, $plazaId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $plazaId = PlazaId::fromNullableInt(null);
        $this->assertNull($plazaId);
    }

}
