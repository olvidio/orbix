<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EquipajeIdsActiv;
use Tests\myTest;

class EquipajeIdsActivTest extends myTest
{
    public function test_create_valid_equipajeIdsActiv()
    {
        $equipajeIdsActiv = new EquipajeIdsActiv('3,12,56');
        $this->assertEquals('3,12,56', $equipajeIdsActiv->value());
    }

    public function test_to_string_returns_equipajeIdsActiv_value()
    {
        $equipajeIdsActiv = new EquipajeIdsActiv('3,12,56');
        $this->assertEquals('3,12,56', (string)$equipajeIdsActiv);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $equipajeIdsActiv = EquipajeIdsActiv::fromNullableString('3,12,56');
        $this->assertInstanceOf(EquipajeIdsActiv::class, $equipajeIdsActiv);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $equipajeIdsActiv = EquipajeIdsActiv::fromNullableString(null);
        $this->assertNull($equipajeIdsActiv);
    }

}
