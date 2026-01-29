<?php

namespace Tests\unit\asignaturas\domain\entity;

use src\asignaturas\domain\entity\Departamento;
use src\asignaturas\domain\value_objects\DepartamentoId;
use src\asignaturas\domain\value_objects\DepartamentoName;
use Tests\myTest;

class DepartamentoTest extends myTest
{
    private Departamento $Departamento;

    public function setUp(): void
    {
        parent::setUp();
        $this->Departamento = new Departamento();
        $this->Departamento->setId_departamento(1);
        $this->Departamento->setNombreDepartamentoVo(new DepartamentoName('Test value'));
    }

    public function test_get_id_departamento()
    {
        $this->assertEquals(1, $this->Departamento->getId_departamento());
    }

    public function test_set_and_get_nombre_departamento()
    {
        $nombre_departamentoVo = new DepartamentoName('Test value');
        $this->Departamento->setNombreDepartamentoVo($nombre_departamentoVo);
        $this->assertInstanceOf(DepartamentoName::class, $this->Departamento->getNombreDepartamentoVo());
        $this->assertEquals('Test value', $this->Departamento->getNombreDepartamentoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $departamento = new Departamento();
        $attributes = [
            'id_departamento' => new DepartamentoId(1),
            'nombre_departamento' => new DepartamentoName('Test value'),
        ];
        $departamento->setAllAttributes($attributes);

        $this->assertEquals(1, $departamento->getIdDepartamentoVo()->value());
        $this->assertEquals('Test value', $departamento->getNombreDepartamentoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $departamento = new Departamento();
        $attributes = [
            'id_departamento' => 1,
            'nombre_departamento' => 'Test value',
        ];
        $departamento->setAllAttributes($attributes);

        $this->assertEquals(1, $departamento->getIdDepartamentoVo()->value());
        $this->assertEquals('Test value', $departamento->getNombreDepartamentoVo()->value());
    }
}
