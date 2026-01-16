<?php

namespace Tests\unit\actividadplazas\domain\value_objects;

use src\actividadplazas\domain\value_objects\PlazasNumero;
use Tests\myTest;

class PlazasNumeroTest extends myTest
{
    public function test_create_valid_plazasNumero()
    {
        $plazasNumero = new PlazasNumero(123);
        $this->assertEquals(123, $plazasNumero->value());
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $plazasNumero = PlazasNumero::fromNullableInt(123);
        $this->assertInstanceOf(PlazasNumero::class, $plazasNumero);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $plazasNumero = PlazasNumero::fromNullableInt(null);
        $this->assertNull($plazasNumero);
    }

}
