<?php

namespace Tests\unit\zonassacd\domain\value_objects;

use src\zonassacd\domain\value_objects\NombreGrupoZona;
use Tests\myTest;

class NombreGrupoZonaTest extends myTest
{
    public function test_create_valid_nombreGrupoZona()
    {
        $nombreGrupoZona = new NombreGrupoZona('test value');
        $this->assertEquals('test value', $nombreGrupoZona->value());
    }

    public function test_empty_nombreGrupoZona_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new NombreGrupoZona('');
    }

    public function test_equals_returns_true_for_same_nombreGrupoZona()
    {
        $nombreGrupoZona1 = new NombreGrupoZona('test value');
        $nombreGrupoZona2 = new NombreGrupoZona('test value');
        $this->assertTrue($nombreGrupoZona1->equals($nombreGrupoZona2));
    }

    public function test_equals_returns_false_for_different_nombreGrupoZona()
    {
        $nombreGrupoZona1 = new NombreGrupoZona('test value');
        $nombreGrupoZona2 = new NombreGrupoZona('alternative value');
        $this->assertFalse($nombreGrupoZona1->equals($nombreGrupoZona2));
    }

    public function test_to_string_returns_nombreGrupoZona_value()
    {
        $nombreGrupoZona = new NombreGrupoZona('test value');
        $this->assertEquals('test value', (string)$nombreGrupoZona);
    }

}
