<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoModHorarioId;
use Tests\myTest;

class EncargoModHorarioIdTest extends myTest
{
    public function test_create_valid_encargoModHorarioId()
    {
        $encargoModHorarioId = new EncargoModHorarioId(EncargoModHorarioId::HORARIO_OPCIONAL);
        $this->assertEquals(1, $encargoModHorarioId->value());
    }

    public function test_equals_returns_true_for_same_encargoModHorarioId()
    {
        $encargoModHorarioId1 = new EncargoModHorarioId(EncargoModHorarioId::HORARIO_OPCIONAL);
        $encargoModHorarioId2 = new EncargoModHorarioId(EncargoModHorarioId::HORARIO_OPCIONAL);
        $this->assertTrue($encargoModHorarioId1->equals($encargoModHorarioId2));
    }

    public function test_equals_returns_false_for_different_encargoModHorarioId()
    {
        $encargoModHorarioId1 = new EncargoModHorarioId(EncargoModHorarioId::HORARIO_OPCIONAL);
        $encargoModHorarioId2 = new EncargoModHorarioId(EncargoModHorarioId::HORARIO_POR_MODULOS);
        $this->assertFalse($encargoModHorarioId1->equals($encargoModHorarioId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $encargoModHorarioId = EncargoModHorarioId::fromNullableInt(EncargoModHorarioId::HORARIO_OPCIONAL);
        $this->assertInstanceOf(EncargoModHorarioId::class, $encargoModHorarioId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $encargoModHorarioId = EncargoModHorarioId::fromNullableInt(null);
        $this->assertNull($encargoModHorarioId);
    }

}
