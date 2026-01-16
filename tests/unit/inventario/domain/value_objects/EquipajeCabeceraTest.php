<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EquipajeCabecera;
use Tests\myTest;

class EquipajeCabeceraTest extends myTest
{
    public function test_create_valid_equipajeCabecera()
    {
        $equipajeCabecera = new EquipajeCabecera('test value');
        $this->assertEquals('test value', $equipajeCabecera->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EquipajeCabecera(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_equipajeCabecera_value()
    {
        $equipajeCabecera = new EquipajeCabecera('test value');
        $this->assertEquals('test value', (string)$equipajeCabecera);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $equipajeCabecera = EquipajeCabecera::fromNullableString('test value');
        $this->assertInstanceOf(EquipajeCabecera::class, $equipajeCabecera);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $equipajeCabecera = EquipajeCabecera::fromNullableString(null);
        $this->assertNull($equipajeCabecera);
    }

}
