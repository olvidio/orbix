<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EquipajeLugar;
use Tests\myTest;

class EquipajeLugarTest extends myTest
{
    public function test_create_valid_equipajeLugar()
    {
        $equipajeLugar = new EquipajeLugar('test value');
        $this->assertEquals('test value', $equipajeLugar->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EquipajeLugar(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_equipajeLugar_value()
    {
        $equipajeLugar = new EquipajeLugar('test value');
        $this->assertEquals('test value', (string)$equipajeLugar);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $equipajeLugar = EquipajeLugar::fromNullableString('test value');
        $this->assertInstanceOf(EquipajeLugar::class, $equipajeLugar);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $equipajeLugar = EquipajeLugar::fromNullableString(null);
        $this->assertNull($equipajeLugar);
    }

}
