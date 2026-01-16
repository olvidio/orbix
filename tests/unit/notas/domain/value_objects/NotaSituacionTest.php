<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\NotaSituacion;
use Tests\myTest;

class NotaSituacionTest extends myTest
{
    public function test_create_valid_notaSituacion()
    {
        $notaSituacion = new NotaSituacion(NotaSituacion::DESCONOCIDO);
        $this->assertEquals(0, $notaSituacion->value());
    }

    public function test_to_string_returns_notaSituacion_value()
    {
        $notaSituacion = new NotaSituacion(NotaSituacion::NUMERICA);
        $this->assertEquals(NotaSituacion::NUMERICA, (string)$notaSituacion);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $notaSituacion = NotaSituacion::fromNullableInt(NotaSituacion::DESCONOCIDO);
        $this->assertInstanceOf(NotaSituacion::class, $notaSituacion);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $notaSituacion = NotaSituacion::fromNullableInt(null);
        $this->assertNull($notaSituacion);
    }

}
