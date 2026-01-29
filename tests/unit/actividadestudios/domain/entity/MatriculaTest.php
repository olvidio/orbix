<?php

namespace Tests\unit\actividadestudios\domain\entity;

use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadestudios\domain\entity\Matricula;
use src\actividadestudios\domain\value_objects\Acta;
use src\actividadestudios\domain\value_objects\NotaMax;
use src\actividadestudios\domain\value_objects\NotaNum;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\value_objects\NotaSituacion;
use Tests\myTest;

class MatriculaTest extends myTest
{
    private Matricula $Matricula;

    public function setUp(): void
    {
        parent::setUp();
        $this->Matricula = new Matricula();
        $this->Matricula->setId_activ(1);
        $this->Matricula->setIdAsignaturaVo(new AsignaturaId(1001));
    }

    public function test_set_and_get_id_activ()
    {
        $this->Matricula->setId_activ(1);
        $this->assertEquals(1, $this->Matricula->getId_activ());
    }

    public function test_set_and_get_id_asignatura()
    {
        $id_asignaturaVo = new AsignaturaId(1001);
        $this->Matricula->setIdAsignaturaVo($id_asignaturaVo);
        $this->assertInstanceOf(AsignaturaId::class, $this->Matricula->getIdAsignaturaVo());
        $this->assertEquals(1001, $this->Matricula->getIdAsignaturaVo()->value());
    }

    public function test_set_and_get_id_nom()
    {
        $this->Matricula->setId_nom(1);
        $this->assertEquals(1, $this->Matricula->getId_nom());
    }

    public function test_set_and_get_id_situacion()
    {
        $id_situacionVo = new NotaSituacion(1);
        $this->Matricula->setIdSituacionVo($id_situacionVo);
        $this->assertInstanceOf(NotaSituacion::class, $this->Matricula->getIdSituacionVo());
        $this->assertEquals(1, $this->Matricula->getIdSituacionVo()->value());
    }

    public function test_set_and_get_preceptor()
    {
        $this->Matricula->setPreceptor(true);
        $this->assertTrue($this->Matricula->isPreceptor());
    }

    public function test_set_and_get_id_nivel()
    {
        $id_nivelVo = new NivelStgrId(1);
        $this->Matricula->setIdNivelVo($id_nivelVo);
        $this->assertInstanceOf(NivelStgrId::class, $this->Matricula->getIdNivelVo());
        $this->assertEquals(1, $this->Matricula->getIdNivelVo()->value());
    }

    public function test_set_and_get_nota_num()
    {
        $nota_numVo = new NotaNum(1);
        $this->Matricula->setNotaNumVo($nota_numVo);
        $this->assertInstanceOf(NotaNum::class, $this->Matricula->getNotaNumVo());
        $this->assertEquals(1, $this->Matricula->getNotaNumVo()->value());
    }

    public function test_set_and_get_nota_max()
    {
        $nota_maxVo = new NotaMax(1);
        $this->Matricula->setNotaMaxVo($nota_maxVo);
        $this->assertInstanceOf(NotaMax::class, $this->Matricula->getNotaMaxVo());
        $this->assertEquals(1, $this->Matricula->getNotaMaxVo()->value());
    }

    public function test_set_and_get_id_preceptor()
    {
        $this->Matricula->setId_preceptor(1);
        $this->assertEquals(1, $this->Matricula->getId_preceptor());
    }

    public function test_set_and_get_acta()
    {
        $actaVo = new Acta('test');
        $this->Matricula->setActaVo($actaVo);
        $this->assertInstanceOf(Acta::class, $this->Matricula->getActaVo());
        $this->assertEquals('test', $this->Matricula->getActaVo()->value());
    }

    public function test_set_all_attributes()
    {
        $matricula = new Matricula();
        $attributes = [
            'id_activ' => 1,
            'id_asignatura' => new AsignaturaId(1001),
            'id_nom' => 1,
            'id_situacion' => new NotaSituacion(1),
            'preceptor' => true,
            'id_nivel' => new NivelStgrId(1),
            'nota_num' => new NotaNum(1),
            'nota_max' => new NotaMax(1),
            'id_preceptor' => 1,
            'acta' => new Acta('test'),
        ];
        $matricula->setAllAttributes($attributes);

        $this->assertEquals(1, $matricula->getId_activ());
        $this->assertEquals(1001, $matricula->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $matricula->getId_nom());
        $this->assertEquals(1, $matricula->getIdSituacionVo()->value());
        $this->assertTrue($matricula->isPreceptor());
        $this->assertEquals(1, $matricula->getIdNivelVo()->value());
        $this->assertEquals(1, $matricula->getNotaNumVo()->value());
        $this->assertEquals(1, $matricula->getNotaMaxVo()->value());
        $this->assertEquals(1, $matricula->getId_preceptor());
        $this->assertEquals('test', $matricula->getActaVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $matricula = new Matricula();
        $attributes = [
            'id_activ' => 1,
            'id_asignatura' => 1001,
            'id_nom' => 1,
            'id_situacion' => 1,
            'preceptor' => true,
            'id_nivel' => 1,
            'nota_num' => 1,
            'nota_max' => 1,
            'id_preceptor' => 1,
            'acta' => 'test',
        ];
        $matricula->setAllAttributes($attributes);

        $this->assertEquals(1, $matricula->getId_activ());
        $this->assertEquals(1001, $matricula->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $matricula->getId_nom());
        $this->assertEquals(1, $matricula->getIdSituacionVo()->value());
        $this->assertTrue($matricula->isPreceptor());
        $this->assertEquals(1, $matricula->getIdNivelVo()->value());
        $this->assertEquals(1, $matricula->getNotaNumVo()->value());
        $this->assertEquals(1, $matricula->getNotaMaxVo()->value());
        $this->assertEquals(1, $matricula->getId_preceptor());
        $this->assertEquals('test', $matricula->getActaVo()->value());
    }
}
