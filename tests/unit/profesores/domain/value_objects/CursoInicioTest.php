<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\CursoInicio;
use Tests\myTest;

class CursoInicioTest extends myTest
{
    //// año académico inicio, permitir 1900-2100
    public function test_create_valid_cursoInicio()
    {
        $cursoInicio = new CursoInicio(2025);
        $this->assertEquals(2025, $cursoInicio->value());
    }

    public function test_to_string_returns_cursoInicio_value()
    {
        $cursoInicio = new CursoInicio(2025);
        $this->assertEquals(2025, (string)$cursoInicio);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $cursoInicio = CursoInicio::fromNullableInt(2025);
        $this->assertInstanceOf(CursoInicio::class, $cursoInicio);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $cursoInicio = CursoInicio::fromNullableInt(null);
        $this->assertNull($cursoInicio);
    }

}
