<?php

namespace Tests\unit\profesores\domain\entity;

use src\profesores\domain\entity\ProfesorTipo;
use src\profesores\domain\value_objects\ProfesorTipoId;
use src\profesores\domain\value_objects\ProfesorTipoName;
use Tests\myTest;

class ProfesorTipoTest extends myTest
{
    private ProfesorTipo $ProfesorTipo;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorTipo = new ProfesorTipo();
        $this->ProfesorTipo->setIdTipoProfesorVo(new ProfesorTipoId(1));
    }

    public function test_set_and_get_id_tipo_profesor()
    {
        $id_tipo_profesorVo = new ProfesorTipoId(1);
        $this->ProfesorTipo->setIdTipoProfesorVo($id_tipo_profesorVo);
        $this->assertInstanceOf(ProfesorTipoId::class, $this->ProfesorTipo->getIdTipoProfesorVo());
        $this->assertEquals(1, $this->ProfesorTipo->getIdTipoProfesorVo()->value());
    }

    public function test_set_and_get_tipo_profesor()
    {
        $tipo_profesorVo = new ProfesorTipoName('Test value');
        $this->ProfesorTipo->setTipoProfesorVo($tipo_profesorVo);
        $this->assertInstanceOf(ProfesorTipoName::class, $this->ProfesorTipo->getTipoProfesorVo());
        $this->assertEquals('Test value', $this->ProfesorTipo->getTipoProfesorVo()->value());
    }

    public function test_set_all_attributes()
    {
        $profesorTipo = new ProfesorTipo();
        $attributes = [
            'id_tipo_profesor' => new ProfesorTipoId(1),
            'tipo_profesor' => new ProfesorTipoName('Test value'),
        ];
        $profesorTipo->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorTipo->getIdTipoProfesorVo()->value());
        $this->assertEquals('Test value', $profesorTipo->getTipoProfesorVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorTipo = new ProfesorTipo();
        $attributes = [
            'id_tipo_profesor' => 1,
            'tipo_profesor' => 'Test value',
        ];
        $profesorTipo->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorTipo->getIdTipoProfesorVo()->value());
        $this->assertEquals('Test value', $profesorTipo->getTipoProfesorVo()->value());
    }
}
