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
        new EquipajePie(str_repeat('a', 1000)); // Assuming max length validation
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

}
