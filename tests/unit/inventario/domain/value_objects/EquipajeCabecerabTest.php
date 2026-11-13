<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EquipajeCabecerab;
use Tests\myTest;

class EquipajeCabecerabTest extends myTest
{
    public function test_create_valid_equipajeCabecerab()
    {
        $equipajeCabecerab = new EquipajeCabecerab('test value');
        $this->assertEquals('test value', $equipajeCabecerab->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EquipajeCabecerab(str_repeat('a', 401));
    }

    public function test_to_string_returns_equipajeCabecerab_value()
    {
        $equipajeCabecerab = new EquipajeCabecerab('test value');
        $this->assertEquals('test value', (string)$equipajeCabecerab);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $equipajeCabecerab = EquipajeCabecerab::fromNullableString('test value');
        $this->assertInstanceOf(EquipajeCabecerab::class, $equipajeCabecerab);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $equipajeCabecerab = EquipajeCabecerab::fromNullableString(null);
        $this->assertNull($equipajeCabecerab);
    }


    /** Hay textos de 359 caracteres en la BD dev (columna text): antes el límite era menor y el equipaje no se podía cargar. */
    public function test_admite_los_textos_largos_que_hay_en_la_bd(): void
    {
        $this->assertSame(400, mb_strlen((new EquipajeCabecerab(str_repeat('á', 400)))->value()));
    }
}
