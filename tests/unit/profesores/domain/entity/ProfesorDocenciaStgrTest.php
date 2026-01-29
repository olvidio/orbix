<?php

namespace Tests\unit\profesores\domain\entity;

use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\value_objects\ActaNumero;
use src\procesos\domain\value_objects\ActividadId;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use src\profesores\domain\value_objects\Acta;
use src\profesores\domain\value_objects\CursoInicio;
use src\profesores\domain\value_objects\ProfesorTipoName;
use Tests\myTest;

class ProfesorDocenciaStgrTest extends myTest
{
    private ProfesorDocenciaStgr $ProfesorDocenciaStgr;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorDocenciaStgr = new ProfesorDocenciaStgr();
        $this->ProfesorDocenciaStgr->setId_item(1);
        $this->ProfesorDocenciaStgr->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ProfesorDocenciaStgr->setId_item(1);
        $this->assertEquals(1, $this->ProfesorDocenciaStgr->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorDocenciaStgr->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorDocenciaStgr->getId_nom());
    }

    public function test_set_and_get_id_asignatura()
    {
        $id_asignaturaVo = new AsignaturaId(1001);
        $this->ProfesorDocenciaStgr->setIdAsignaturaVo($id_asignaturaVo);
        $this->assertInstanceOf(AsignaturaId::class, $this->ProfesorDocenciaStgr->getIdAsignaturaVo());
        $this->assertEquals(1001, $this->ProfesorDocenciaStgr->getIdAsignaturaVo()->value());
    }

    public function test_set_and_get_id_activ()
    {
        $id_activVo = new ActividadId(1);
        $this->ProfesorDocenciaStgr->setIdActivVo($id_activVo);
        $this->assertInstanceOf(ActividadId::class, $this->ProfesorDocenciaStgr->getIdActivVo());
        $this->assertEquals(1, $this->ProfesorDocenciaStgr->getIdActivVo()->value());
    }

    public function test_set_and_get_tipo()
    {
        $tipoVo = new TipoActividadAsignatura('v');
        $this->ProfesorDocenciaStgr->setTipoVo($tipoVo);
        $this->assertInstanceOf(TipoActividadAsignatura::class, $this->ProfesorDocenciaStgr->getTipoVo());
        $this->assertEquals('v', $this->ProfesorDocenciaStgr->getTipoVo()->value());
    }

    public function test_set_and_get_curso_inicio()
    {
        $this->ProfesorDocenciaStgr->setCurso_inicio(1);
        $this->assertEquals(1, $this->ProfesorDocenciaStgr->getCurso_inicio());
    }

    public function test_set_and_get_acta()
    {
        $actaVo = new ActaNumero('Test value');
        $this->ProfesorDocenciaStgr->setActaVo($actaVo);
        $this->assertInstanceOf(ActaNumero::class, $this->ProfesorDocenciaStgr->getActaVo());
        $this->assertEquals('Test value', $this->ProfesorDocenciaStgr->getActaVo()->value());
    }

    public function test_set_all_attributes()
    {
        $profesorDocenciaStgr = new ProfesorDocenciaStgr();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_asignatura' => new AsignaturaId(1001),
            'id_activ' => new ActividadId(1),
            'tipo' => new TipoActividadAsignatura('v'),
            'curso_inicio' => 1,
            'acta' => new ActaNumero('Test value'),
        ];
        $profesorDocenciaStgr->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorDocenciaStgr->getId_item());
        $this->assertEquals(1, $profesorDocenciaStgr->getId_nom());
        $this->assertEquals(1001, $profesorDocenciaStgr->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $profesorDocenciaStgr->getIdActivVo()->value());
        $this->assertEquals('v', $profesorDocenciaStgr->getTipoVo()->value());
        $this->assertEquals(1, $profesorDocenciaStgr->getCurso_inicio());
        $this->assertEquals('Test value', $profesorDocenciaStgr->getActaVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorDocenciaStgr = new ProfesorDocenciaStgr();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_asignatura' => 1001,
            'id_activ' => 1,
            'tipo' => 'v',
            'curso_inicio' => 1,
            'acta' => 'Test value',
        ];
        $profesorDocenciaStgr->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorDocenciaStgr->getId_item());
        $this->assertEquals(1, $profesorDocenciaStgr->getId_nom());
        $this->assertEquals(1001, $profesorDocenciaStgr->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $profesorDocenciaStgr->getIdActivVo()->value());
        $this->assertEquals('v', $profesorDocenciaStgr->getTipoVo()->value());
        $this->assertEquals(1, $profesorDocenciaStgr->getCurso_inicio());
        $this->assertEquals('Test value', $profesorDocenciaStgr->getActaVo()->value());
    }
}
