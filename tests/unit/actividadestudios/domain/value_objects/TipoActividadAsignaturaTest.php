<?php

namespace Tests\unit\actividadestudios\domain\value_objects;

use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use Tests\myTest;

class TipoActividadAsignaturaTest extends myTest
{
    public function test_create_valid_tipoActividadAsignatura()
    {
        $tipoActividadAsignatura = new TipoActividadAsignatura(TipoActividadAsignatura::TIPO_CA);
        $this->assertEquals(TipoActividadAsignatura::TIPO_CA, $tipoActividadAsignatura->value());
    }

    public function test_to_string_returns_tipoActividadAsignatura_value()
    {
        $tipoActividadAsignatura = new TipoActividadAsignatura(TipoActividadAsignatura::TIPO_CA);
        $this->assertEquals(TipoActividadAsignatura::TIPO_CA, (string)$tipoActividadAsignatura);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoActividadAsignatura = TipoActividadAsignatura::fromNullableString(TipoActividadAsignatura::TIPO_CA);
        $this->assertInstanceOf(TipoActividadAsignatura::class, $tipoActividadAsignatura);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoActividadAsignatura = TipoActividadAsignatura::fromNullableString(null);
        $this->assertNull($tipoActividadAsignatura);
    }

}
