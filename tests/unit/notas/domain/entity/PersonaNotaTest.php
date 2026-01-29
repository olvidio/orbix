<?php

namespace Tests\unit\notas\domain\entity;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Detalle;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaMax;
use src\notas\domain\value_objects\NotaNum;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\procesos\domain\value_objects\ActividadId;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class PersonaNotaTest extends myTest
{
    private PersonaNota $PersonaNota;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaNota = new PersonaNota();
        $this->PersonaNota->setId_schema(1);
        $this->PersonaNota->setId_nom(1);
    }

    public function test_set_and_get_id_schema()
    {
        $this->PersonaNota->setId_schema(1);
        $this->assertEquals(1, $this->PersonaNota->getId_schema());
    }

    public function test_set_and_get_id_nom()
    {
        $this->PersonaNota->setId_nom(1);
        $this->assertEquals(1, $this->PersonaNota->getId_nom());
    }

    public function test_set_and_get_id_nivel()
    {
        $this->PersonaNota->setId_nivel(1);
        $this->assertEquals(1, $this->PersonaNota->getId_nivel());
    }

    public function test_set_and_get_id_asignatura()
    {
        $id_asignaturaVo = new AsignaturaId(1001);
        $this->PersonaNota->setIdAsignaturaVo($id_asignaturaVo);
        $this->assertInstanceOf(AsignaturaId::class, $this->PersonaNota->getIdAsignaturaVo());
        $this->assertEquals(1001, $this->PersonaNota->getIdAsignaturaVo()->value());
    }

    public function test_set_and_get_id_situacion()
    {
        $id_situacionVo = new NotaSituacion(1);
        $this->PersonaNota->setIdSituacionVo($id_situacionVo);
        $this->assertInstanceOf(NotaSituacion::class, $this->PersonaNota->getIdSituacionVo());
        $this->assertEquals(1, $this->PersonaNota->getIdSituacionVo()->value());
    }

    public function test_set_and_get_acta()
    {
        $actaVo = new ActaNumero('dlb 23/24');
        $this->PersonaNota->setActaVo($actaVo);
        $this->assertInstanceOf(ActaNumero::class, $this->PersonaNota->getActaVo());
        $this->assertEquals('dlb 23/24', $this->PersonaNota->getActaVo()->value());
    }

    public function test_set_and_get_f_acta()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->PersonaNota->setF_acta($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->PersonaNota->getF_acta());
        $this->assertEquals('2024-01-15 10:30:00', $this->PersonaNota->getF_acta()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_detalle()
    {
        $detalleVo = new Detalle('test');
        $this->PersonaNota->setDetalleVo($detalleVo);
        $this->assertInstanceOf(Detalle::class, $this->PersonaNota->getDetalleVo());
        $this->assertEquals('test', $this->PersonaNota->getDetalleVo()->value());
    }

    public function test_set_and_get_preceptor()
    {
        $this->PersonaNota->setPreceptor(true);
        $this->assertTrue($this->PersonaNota->isPreceptor());
    }

    public function test_set_and_get_id_preceptor()
    {
        $this->PersonaNota->setId_preceptor(1);
        $this->assertEquals(1, $this->PersonaNota->getId_preceptor());
    }

    public function test_set_and_get_epoca()
    {
        $epocaVo = new NotaEpoca(1);
        $this->PersonaNota->setEpocaVo($epocaVo);
        $this->assertInstanceOf(NotaEpoca::class, $this->PersonaNota->getEpocaVo());
        $this->assertEquals(1, $this->PersonaNota->getEpocaVo()->value());
    }

    public function test_set_and_get_id_activ()
    {
        $id_activVo = new ActividadId(1);
        $this->PersonaNota->setIdActivVo($id_activVo);
        $this->assertInstanceOf(ActividadId::class, $this->PersonaNota->getIdActivVo());
        $this->assertEquals(1, $this->PersonaNota->getIdActivVo()->value());
    }

    public function test_set_and_get_nota_num()
    {
        $nota_numVo = new NotaNum(7.5);
        $this->PersonaNota->setNotaNumVo($nota_numVo);
        $this->assertInstanceOf(NotaNum::class, $this->PersonaNota->getNotaNumVo());
        $this->assertEquals(7.5, $this->PersonaNota->getNotaNumVo()->value());
    }

    public function test_set_and_get_nota_max()
    {
        $nota_maxVo = new NotaMax(10);
        $this->PersonaNota->setNotaMaxVo($nota_maxVo);
        $this->assertInstanceOf(NotaMax::class, $this->PersonaNota->getNotaMaxVo());
        $this->assertEquals(10, $this->PersonaNota->getNotaMaxVo()->value());
    }

    public function test_set_and_get_tipo_acta()
    {
        $tipo_actaVo = new TipoActa(1);
        $this->PersonaNota->setTipoActaVo($tipo_actaVo);
        $this->assertInstanceOf(TipoActa::class, $this->PersonaNota->getTipoActaVo());
        $this->assertEquals(1, $this->PersonaNota->getTipoActaVo()->value());
    }

    public function test_set_all_attributes()
    {
        $personaNota = new PersonaNota();
        $attributes = [
            'id_schema' => 1,
            'id_nom' => 1,
            'id_nivel' => 1,
            'id_asignatura' => new AsignaturaId(1001),
            'id_situacion' => new NotaSituacion(1),
            'acta' => new ActaNumero('dlb 23/24'),
            'f_acta' => new DateTimeLocal('2024-01-15 10:30:00'),
            'detalle' => new Detalle('test'),
            'preceptor' => true,
            'id_preceptor' => 1,
            'epoca' => new NotaEpoca(1),
            'id_activ' => new ActividadId(1),
            'nota_num' => new NotaNum(7.5),
            'nota_max' => new NotaMax(10),
            'tipo_acta' => new TipoActa(1),
        ];
        $personaNota->setAllAttributes($attributes);

        $this->assertEquals(1, $personaNota->getId_schema());
        $this->assertEquals(1, $personaNota->getId_nom());
        $this->assertEquals(1, $personaNota->getId_nivel());
        $this->assertEquals(1001, $personaNota->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $personaNota->getIdSituacionVo()->value());
        $this->assertEquals('dlb 23/24', $personaNota->getActaVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaNota->getF_acta()->format('Y-m-d H:i:s'));
        $this->assertEquals('test', $personaNota->getDetalleVo()->value());
        $this->assertTrue($personaNota->isPreceptor());
        $this->assertEquals(1, $personaNota->getId_preceptor());
        $this->assertEquals(1, $personaNota->getEpocaVo()->value());
        $this->assertEquals(1, $personaNota->getIdActivVo()->value());
        $this->assertEquals(7.5, $personaNota->getNotaNumVo()->value());
        $this->assertEquals(10, $personaNota->getNotaMaxVo()->value());
        $this->assertEquals(1, $personaNota->getTipoActaVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaNota = new PersonaNota();
        $attributes = [
            'id_schema' => 1,
            'id_nom' => 1,
            'id_nivel' => 1,
            'id_asignatura' => 1001,
            'id_situacion' => 1,
            'acta' => 'dlb 23/24',
            'f_acta' => new DateTimeLocal('2024-01-15 10:30:00'),
            'detalle' => 'test',
            'preceptor' => true,
            'id_preceptor' => 1,
            'epoca' => 1,
            'id_activ' => 1,
            'nota_num' => 7.5,
            'nota_max' => 10,
            'tipo_acta' => 1,
        ];
        $personaNota->setAllAttributes($attributes);

        $this->assertEquals(1, $personaNota->getId_schema());
        $this->assertEquals(1, $personaNota->getId_nom());
        $this->assertEquals(1, $personaNota->getId_nivel());
        $this->assertEquals(1001, $personaNota->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $personaNota->getIdSituacionVo()->value());
        $this->assertEquals('dlb 23/24', $personaNota->getActaVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaNota->getF_acta()->format('Y-m-d H:i:s'));
        $this->assertEquals('test', $personaNota->getDetalleVo()->value());
        $this->assertTrue($personaNota->isPreceptor());
        $this->assertEquals(1, $personaNota->getId_preceptor());
        $this->assertEquals(1, $personaNota->getEpocaVo()->value());
        $this->assertEquals(1, $personaNota->getIdActivVo()->value());
        $this->assertEquals(7.5, $personaNota->getNotaNumVo()->value());
        $this->assertEquals(10, $personaNota->getNotaMaxVo()->value());
        $this->assertEquals(1, $personaNota->getTipoActaVo()->value());
    }
}
