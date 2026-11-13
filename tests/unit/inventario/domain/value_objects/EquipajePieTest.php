<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EquipajePie;
use Tests\myTest;

class EquipajePieTest extends myTest
{
    public function test_create_valid_equipajePie()
    {
        $equipajePie = new EquipajePie('test value');
        $this->assertEquals('test value', $equipajePie->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EquipajePie(str_repeat('a', 2001));
    }

    public function test_to_string_returns_equipajePie_value()
    {
        $equipajePie = new EquipajePie('test value');
        $this->assertEquals('test value', (string)$equipajePie);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $equipajePie = EquipajePie::fromNullableString('test value');
        $this->assertInstanceOf(EquipajePie::class, $equipajePie);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $equipajePie = EquipajePie::fromNullableString(null);
        $this->assertNull($equipajePie);
    }


    /** Hay textos de 1031 caracteres en la BD dev (columna text): antes el límite era menor y el equipaje no se podía cargar. */
    public function test_admite_los_textos_largos_que_hay_en_la_bd(): void
    {
        $this->assertSame(2000, mb_strlen((new EquipajePie(str_repeat('á', 2000)))->value()));
    }
}
