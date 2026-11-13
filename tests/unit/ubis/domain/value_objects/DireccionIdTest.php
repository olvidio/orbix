<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DireccionId;
use Tests\myTest;

class DireccionIdTest extends myTest
{
    public function test_create_valid_direccionId()
    {
        $direccionId = new DireccionId(300514);
        $this->assertEquals(300514, $direccionId->value());
    }

    public function test_to_string_returns_direccionId_value()
    {
        $direccionId = new DireccionId(300514);
        $this->assertEquals(300514, (string)$direccionId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $direccionId = DireccionId::fromNullableInt(300514);
        $this->assertInstanceOf(DireccionId::class, $direccionId);
    }



    /** Direcciones de casas de fuera con id de esquema 3 (había una en la BD dev): antes se rechazaban. */
    public function test_acepta_ids_negativos_de_los_tres_tipos_y_rechaza_los_demas(): void
    {
        foreach ([-10019254, -20019254, -30019254] as $id) {
            $this->assertSame($id, (new DireccionId($id))->value());
        }
        foreach ([-40019254, 40019254, 1234, -1234] as $id) {
            try {
                new DireccionId($id);
                $this->fail("debería rechazar $id");
            } catch (\InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
