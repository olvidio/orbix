<?php

namespace Tests\unit\actividadplazas\domain\value_objects;

use src\actividadplazas\domain\value_objects\PeticionOrden;
use Tests\myTest;

class PeticionOrdenTest extends myTest
{
    public function test_create_valid_peticionOrden()
    {
        $peticionOrden = new PeticionOrden(123);
        $this->assertEquals(123, $peticionOrden->value());
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $peticionOrden = PeticionOrden::fromNullableInt(123);
        $this->assertInstanceOf(PeticionOrden::class, $peticionOrden);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $peticionOrden = PeticionOrden::fromNullableInt(null);
        $this->assertNull($peticionOrden);
    }

}
