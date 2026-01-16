<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EquipajeNom;
use Tests\myTest;

class EquipajeNomTest extends myTest
{
    public function test_create_valid_equipajeNom()
    {
        $equipajeNom = new EquipajeNom('test value');
        $this->assertEquals('test value', $equipajeNom->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EquipajeNom(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_equipajeNom_value()
    {
        $equipajeNom = new EquipajeNom('test value');
        $this->assertEquals('test value', (string)$equipajeNom);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $equipajeNom = EquipajeNom::fromNullableString('test value');
        $this->assertInstanceOf(EquipajeNom::class, $equipajeNom);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $equipajeNom = EquipajeNom::fromNullableString(null);
        $this->assertNull($equipajeNom);
    }

}
