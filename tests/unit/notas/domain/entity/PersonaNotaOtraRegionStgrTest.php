<?php

namespace Tests\unit\notas\domain\entity;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
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

class PersonaNotaOtraRegionStgrTest extends myTest
{
    private PersonaNotaOtraRegionStgr $PersonaNotaOtraRegionStgr;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaNotaOtraRegionStgr = new PersonaNotaOtraRegionStgr();
        $this->PersonaNotaOtraRegionStgr->setId_schema(1);
        $this->PersonaNotaOtraRegionStgr->setId_nom(1);
    }

    public function test_set_and_get_id_schema()
    {
        $this->PersonaNotaOtraRegionStgr->setId_schema(1);
        $this->assertEquals(1, $this->PersonaNotaOtraRegionStgr->getId_schema());
    }

    public function test_set_and_get_id_nom()
    {
        $this->PersonaNotaOtraRegionStgr->setId_nom(1);
        $this->assertEquals(1, $this->PersonaNotaOtraRegionStgr->getId_nom());
    }

    public function test_set_and_get_id_nivel()
    {
        $this->PersonaNotaOtraRegionStgr->setId_nivel(1);
        $this->assertEquals(1, $this->PersonaNotaOtraRegionStgr->getId_nivel());
    }

    public function test_set_and_get_id_asignatura()
    {
        $id_asignaturaVo = new AsignaturaId(1001);
        $this->PersonaNotaOtraRegionStgr->setIdAsignaturaVo($id_asignaturaVo);
        $this->assertInstanceOf(AsignaturaId::class, $this->PersonaNotaOtraRegionStgr->getIdAsignaturaVo());
        $this->assertEquals(1001, $this->PersonaNotaOtraRegionStgr->getIdAsignaturaVo()->value());
    }

    public function test_set_and_get_id_situacion()
    {
        $id_situacionVo = new NotaSituacion(1);
        $this->PersonaNotaOtraRegionStgr->setIdSituacionVo($id_situacionVo);
        $this->assertInstanceOf(NotaSituacion::class, $this->PersonaNotaOtraRegionStgr->getIdSituacionVo());
        $this->assertEquals(1, $this->PersonaNotaOtraRegionStgr->getIdSituacionVo()->value());
    }

    public function test_set_and_get_acta()
    {
        $actaVo = new ActaNumero('dlb 23/24');
        $this->PersonaNotaOtraRegionStgr->setActaVo($actaVo);
        $this->assertInstanceOf(ActaNumero::class, $this->PersonaNotaOtraRegionStgr->getActaVo());
        $this->assertEquals('dlb 23/24', $this->PersonaNotaOtraRegionStgr->getActaVo()->value());
    }

    public function test_set_and_get_f_acta()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->PersonaNotaOtraRegionStgr->setF_acta($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->PersonaNotaOtraRegionStgr->getF_acta());
        $this->assertEquals('2024-01-15 10:30:00', $this->PersonaNotaOtraRegionStgr->getF_acta()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_detalle()
    {
        $detalleVo = new Detalle('test');
        $this->PersonaNotaOtraRegionStgr->setDetalleVo($detalleVo);
        $this->assertInstanceOf(Detalle::class, $this->PersonaNotaOtraRegionStgr->getDetalleVo());
        $this->assertEquals('test', $this->PersonaNotaOtraRegionStgr->getDetalleVo()->value());
    }

    public function test_set_and_get_preceptor()
    {
        $this->PersonaNotaOtraRegionStgr->setPreceptor(true);
        $this->assertTrue($this->PersonaNotaOtraRegionStgr->isPreceptor());
    }

    public function test_set_and_get_id_preceptor()
    {
        $this->PersonaNotaOtraRegionStgr->setId_preceptor(1);
        $this->assertEquals(1, $this->PersonaNotaOtraRegionStgr->getId_preceptor());
    }

    public function test_set_and_get_epoca()
    {
        $epocaVo = new NotaEpoca(1);
        $this->PersonaNotaOtraRegionStgr->setEpocaVo($epocaVo);
        $this->assertInstanceOf(NotaEpoca::class, $this->PersonaNotaOtraRegionStgr->getEpocaVo());
        $this->assertEquals(1, $this->PersonaNotaOtraRegionStgr->getEpocaVo()->value());
    }

    public function test_set_and_get_id_activ()
    {
        $id_activVo = new ActividadId(1);
        $this->PersonaNotaOtraRegionStgr->setIdActivVo($id_activVo);
        $this->assertInstanceOf(ActividadId::class, $this->PersonaNotaOtraRegionStgr->getIdActivVo());
        $this->assertEquals(1, $this->PersonaNotaOtraRegionStgr->getIdActivVo()->value());
    }

    public function test_set_and_get_nota_num()
    {
        $nota_numVo = new NotaNum(7.5);
        $this->PersonaNotaOtraRegionStgr->setNotaNumVo($nota_numVo);
        $this->assertInstanceOf(NotaNum::class, $this->PersonaNotaOtraRegionStgr->getNotaNumVo());
        $this->assertEquals(7.5, $this->PersonaNotaOtraRegionStgr->getNotaNumVo()->value());
    }

    public function test_set_and_get_nota_max()
    {
        $nota_maxVo = new NotaMax(10);
        $this->PersonaNotaOtraRegionStgr->setNotaMaxVo($nota_maxVo);
        $this->assertInstanceOf(NotaMax::class, $this->PersonaNotaOtraRegionStgr->getNotaMaxVo());
        $this->assertEquals(10, $this->PersonaNotaOtraRegionStgr->getNotaMaxVo()->value());
    }

    public function test_set_and_get_tipo_acta()
    {
        $tipo_actaVo = new TipoActa(1);
        $this->PersonaNotaOtraRegionStgr->setTipoActaVo($tipo_actaVo);
        $this->assertInstanceOf(TipoActa::class, $this->PersonaNotaOtraRegionStgr->getTipoActaVo());
        $this->assertEquals(1, $this->PersonaNotaOtraRegionStgr->getTipoActaVo()->value());
    }

    public function test_set_and_get_json_certificados()
    {
        $this->PersonaNotaOtraRegionStgr->setJson_certificados(json_encode([['estado' => 'guardado', 'certificado' => 'H 459/25'], ['estado' => 'guardado', 'certificado' => 'H 23/21']]));
        $this->assertJsonStringEqualsJsonString(
            json_encode([['estado' => 'guardado', 'certificado' => 'H 459/25'], ['estado' => 'guardado', 'certificado' => 'H 23/21']]),
            json_encode($this->PersonaNotaOtraRegionStgr->getJson_certificados())
        );
    }

    public function test_set_all_attributes()
    {
        $personaNotaOtraRegionStgr = new PersonaNotaOtraRegionStgr();
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
            'json_certificados' => json_encode([['estado' => 'guardado', 'certificado' => 'H 459/25'], ['estado' => 'guardado', 'certificado' => 'H 23/21']]),
        ];
        $personaNotaOtraRegionStgr->setAllAttributes($attributes);

        $this->assertEquals(1, $personaNotaOtraRegionStgr->getId_schema());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getId_nom());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getId_nivel());
        $this->assertEquals(1001, $personaNotaOtraRegionStgr->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getIdSituacionVo()->value());
        $this->assertEquals('dlb 23/24', $personaNotaOtraRegionStgr->getActaVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaNotaOtraRegionStgr->getF_acta()->format('Y-m-d H:i:s'));
        $this->assertEquals('test', $personaNotaOtraRegionStgr->getDetalleVo()->value());
        $this->assertTrue($personaNotaOtraRegionStgr->isPreceptor());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getId_preceptor());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getEpocaVo()->value());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getIdActivVo()->value());
        $this->assertEquals(7.5, $personaNotaOtraRegionStgr->getNotaNumVo()->value());
        $this->assertEquals(10, $personaNotaOtraRegionStgr->getNotaMaxVo()->value());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getTipoActaVo()->value());
        $this->assertJsonStringEqualsJsonString(
            json_encode([['estado' => 'guardado', 'certificado' => 'H 459/25'], ['estado' => 'guardado', 'certificado' => 'H 23/21']]),
            json_encode($personaNotaOtraRegionStgr->getJson_certificados())
        );
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaNotaOtraRegionStgr = new PersonaNotaOtraRegionStgr();
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
            'json_certificados' => json_encode([['estado' => 'guardado', 'certificado' => 'H 459/25'], ['estado' => 'guardado', 'certificado' => 'H 23/21']]),
        ];
        $personaNotaOtraRegionStgr->setAllAttributes($attributes);

        $this->assertEquals(1, $personaNotaOtraRegionStgr->getId_schema());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getId_nom());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getId_nivel());
        $this->assertEquals(1001, $personaNotaOtraRegionStgr->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getIdSituacionVo()->value());
        $this->assertEquals('dlb 23/24', $personaNotaOtraRegionStgr->getActaVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaNotaOtraRegionStgr->getF_acta()->format('Y-m-d H:i:s'));
        $this->assertEquals('test', $personaNotaOtraRegionStgr->getDetalleVo()->value());
        $this->assertTrue($personaNotaOtraRegionStgr->isPreceptor());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getId_preceptor());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getEpocaVo()->value());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getIdActivVo()->value());
        $this->assertEquals(7.5, $personaNotaOtraRegionStgr->getNotaNumVo()->value());
        $this->assertEquals(10, $personaNotaOtraRegionStgr->getNotaMaxVo()->value());
        $this->assertEquals(1, $personaNotaOtraRegionStgr->getTipoActaVo()->value());
        $this->assertJsonStringEqualsJsonString(
            json_encode([['estado' => 'guardado', 'certificado' => 'H 459/25'], ['estado' => 'guardado', 'certificado' => 'H 23/21']]),
            json_encode($personaNotaOtraRegionStgr->getJson_certificados())
        );
    }
}
