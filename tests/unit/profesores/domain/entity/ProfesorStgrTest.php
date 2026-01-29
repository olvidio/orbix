<?php

namespace Tests\unit\profesores\domain\entity;

use src\asignaturas\domain\value_objects\DepartamentoId;
use src\profesores\domain\entity\ProfesorStgr;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\profesores\domain\value_objects\ProfesorTipoId;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ProfesorStgrTest extends myTest
{
    private ProfesorStgr $ProfesorStgr;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorStgr = new ProfesorStgr();
        $this->ProfesorStgr->setId_item(1);
        $this->ProfesorStgr->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ProfesorStgr->setId_item(1);
        $this->assertEquals(1, $this->ProfesorStgr->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorStgr->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorStgr->getId_nom());
    }

    public function test_set_and_get_id_departamento()
    {
        $id_departamentoVo = new DepartamentoId(1);
        $this->ProfesorStgr->setIdDepartamentoVo($id_departamentoVo);
        $this->assertInstanceOf(DepartamentoId::class, $this->ProfesorStgr->getIdDepartamentoVo());
        $this->assertEquals(1, $this->ProfesorStgr->getIdDepartamentoVo()->value());
    }

    public function test_set_and_get_escrito_nombramiento()
    {
        $escrito_nombramientoVo = new EscritoNombramiento('Test value');
        $this->ProfesorStgr->setEscritoNombramientoVo($escrito_nombramientoVo);
        $this->assertInstanceOf(EscritoNombramiento::class, $this->ProfesorStgr->getEscritoNombramientoVo());
        $this->assertEquals('Test value', $this->ProfesorStgr->getEscritoNombramientoVo()->value());
    }

    public function test_set_and_get_f_nombramiento()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorStgr->setF_nombramiento($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorStgr->getF_nombramiento());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorStgr->getF_nombramiento()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_id_tipo_profesor()
    {
        $id_tipo_profesorVo = new ProfesorTipoId(1);
        $this->ProfesorStgr->setIdTipoProfesorVo($id_tipo_profesorVo);
        $this->assertInstanceOf(ProfesorTipoId::class, $this->ProfesorStgr->getIdTipoProfesorVo());
        $this->assertEquals(1, $this->ProfesorStgr->getIdTipoProfesorVo()->value());
    }

    public function test_set_and_get_escrito_cese()
    {
        $escrito_ceseVo = new EscritoCese('Test value');
        $this->ProfesorStgr->setEscritoCeseVo($escrito_ceseVo);
        $this->assertInstanceOf(EscritoCese::class, $this->ProfesorStgr->getEscritoCeseVo());
        $this->assertEquals('Test value', $this->ProfesorStgr->getEscritoCeseVo()->value());
    }

    public function test_set_and_get_f_cese()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorStgr->setF_cese($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorStgr->getF_cese());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorStgr->getF_cese()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $profesorStgr = new ProfesorStgr();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_departamento' => new DepartamentoId(1),
            'escrito_nombramiento' => new EscritoNombramiento('Test value'),
            'f_nombramiento' => new DateTimeLocal('2024-01-15 10:30:00'),
            'id_tipo_profesor' => new ProfesorTipoId(1),
            'escrito_cese' => new EscritoCese('Test value'),
            'f_cese' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $profesorStgr->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorStgr->getId_item());
        $this->assertEquals(1, $profesorStgr->getId_nom());
        $this->assertEquals(1, $profesorStgr->getIdDepartamentoVo()->value());
        $this->assertEquals('Test value', $profesorStgr->getEscritoNombramientoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorStgr->getF_nombramiento()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $profesorStgr->getIdTipoProfesorVo()->value());
        $this->assertEquals('Test value', $profesorStgr->getEscritoCeseVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorStgr->getF_cese()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorStgr = new ProfesorStgr();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_departamento' => 1,
            'escrito_nombramiento' => 'Test value',
            'f_nombramiento' => new DateTimeLocal('2024-01-15 10:30:00'),
            'id_tipo_profesor' => 1,
            'escrito_cese' => 'Test value',
            'f_cese' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $profesorStgr->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorStgr->getId_item());
        $this->assertEquals(1, $profesorStgr->getId_nom());
        $this->assertEquals(1, $profesorStgr->getIdDepartamentoVo()->value());
        $this->assertEquals('Test value', $profesorStgr->getEscritoNombramientoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorStgr->getF_nombramiento()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $profesorStgr->getIdTipoProfesorVo()->value());
        $this->assertEquals('Test value', $profesorStgr->getEscritoCeseVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorStgr->getF_cese()->format('Y-m-d H:i:s'));
    }
}
