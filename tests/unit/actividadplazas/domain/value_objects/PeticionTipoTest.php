<?php

namespace Tests\unit\actividadplazas\domain\value_objects;

use src\actividadplazas\domain\value_objects\PeticionTipo;
use Tests\myTest;

class PeticionTipoTest extends myTest
{
    public function test_create_valid_peticionTipo()
    {
        $peticionTipo = new PeticionTipo('test value');
        $this->assertEquals('test value', $peticionTipo->value());
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $peticionTipo = PeticionTipo::fromNullableString('test value');
        $this->assertInstanceOf(PeticionTipo::class, $peticionTipo);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $peticionTipo = PeticionTipo::fromNullableString(null);
        $this->assertNull($peticionTipo);
    }

}
