<?php

namespace Tests\unit\profesores\domain\entity;

use src\asignaturas\domain\value_objects\DepartamentoId;
use src\profesores\domain\entity\ProfesorDirector;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ProfesorDirectorTest extends myTest
{
    private ProfesorDirector $ProfesorDirector;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorDirector = new ProfesorDirector();
        $this->ProfesorDirector->setId_item(1);
        $this->ProfesorDirector->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ProfesorDirector->setId_item(1);
        $this->assertEquals(1, $this->ProfesorDirector->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorDirector->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorDirector->getId_nom());
    }

    public function test_set_and_get_id_departamento()
    {
        $id_departamentoVo = new DepartamentoId(1);
        $this->ProfesorDirector->setIdDepartamentoVo($id_departamentoVo);
        $this->assertInstanceOf(DepartamentoId::class, $this->ProfesorDirector->getIdDepartamentoVo());
        $this->assertEquals(1, $this->ProfesorDirector->getIdDepartamentoVo()->value());
    }

    public function test_set_and_get_escrito_nombramiento()
    {
        $escrito_nombramientoVo = new EscritoNombramiento('Test value');
        $this->ProfesorDirector->setEscritoNombramientoVo($escrito_nombramientoVo);
        $this->assertInstanceOf(EscritoNombramiento::class, $this->ProfesorDirector->getEscritoNombramientoVo());
        $this->assertEquals('Test value', $this->ProfesorDirector->getEscritoNombramientoVo()->value());
    }

    public function test_set_and_get_f_nombramiento()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorDirector->setF_nombramiento($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorDirector->getF_nombramiento());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorDirector->getF_nombramiento()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_escrito_cese()
    {
        $escrito_ceseVo = new EscritoCese('Test value');
        $this->ProfesorDirector->setEscritoCeseVo($escrito_ceseVo);
        $this->assertInstanceOf(EscritoCese::class, $this->ProfesorDirector->getEscritoCeseVo());
        $this->assertEquals('Test value', $this->ProfesorDirector->getEscritoCeseVo()->value());
    }

    public function test_set_and_get_f_cese()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorDirector->setF_cese($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorDirector->getF_cese());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorDirector->getF_cese()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $profesorDirector = new ProfesorDirector();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_departamento' => new DepartamentoId(1),
            'escrito_nombramiento' => new EscritoNombramiento('Test value'),
            'f_nombramiento' => new DateTimeLocal('2024-01-15 10:30:00'),
            'escrito_cese' => new EscritoCese('Test value'),
            'f_cese' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $profesorDirector->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorDirector->getId_item());
        $this->assertEquals(1, $profesorDirector->getId_nom());
        $this->assertEquals(1, $profesorDirector->getIdDepartamentoVo()->value());
        $this->assertEquals('Test value', $profesorDirector->getEscritoNombramientoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorDirector->getF_nombramiento()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $profesorDirector->getEscritoCeseVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorDirector->getF_cese()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorDirector = new ProfesorDirector();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_departamento' => 1,
            'escrito_nombramiento' => 'Test value',
            'f_nombramiento' => new DateTimeLocal('2024-01-15 10:30:00'),
            'escrito_cese' => 'Test value',
            'f_cese' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $profesorDirector->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorDirector->getId_item());
        $this->assertEquals(1, $profesorDirector->getId_nom());
        $this->assertEquals(1, $profesorDirector->getIdDepartamentoVo()->value());
        $this->assertEquals('Test value', $profesorDirector->getEscritoNombramientoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorDirector->getF_nombramiento()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $profesorDirector->getEscritoCeseVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorDirector->getF_cese()->format('Y-m-d H:i:s'));
    }
}
