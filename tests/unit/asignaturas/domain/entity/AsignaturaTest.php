<?php

namespace Tests\unit\asignaturas\domain\entity;

use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\asignaturas\domain\value_objects\AsignaturaName;
use src\asignaturas\domain\value_objects\AsignaturaShortName;
use src\asignaturas\domain\value_objects\AsignaturaTipoId;
use src\asignaturas\domain\value_objects\Creditos;
use src\asignaturas\domain\value_objects\NivelId;
use src\asignaturas\domain\value_objects\SectorId;
use src\asignaturas\domain\value_objects\YearText;
use Tests\myTest;

class AsignaturaTest extends myTest
{
    private Asignatura $Asignatura;

    public function setUp(): void
    {
        parent::setUp();
        $this->Asignatura = new Asignatura();
        $this->Asignatura->setId_asignatura(1001);
        $this->Asignatura->setIdNivelVo(new NivelId(1001));
    }

    public function test_get_id_asignatura()
    {
        $this->assertEquals(1001, $this->Asignatura->getId_asignatura());
    }

    public function test_set_and_get_id_nivel()
    {
        $id_nivelVo = new NivelId(1001);
        $this->Asignatura->setIdNivelVo($id_nivelVo);
        $this->assertInstanceOf(NivelId::class, $this->Asignatura->getIdNivelVo());
        $this->assertEquals(1001, $this->Asignatura->getIdNivelVo()->value());
    }

    public function test_set_and_get_nombre_signatura()
    {
        $this->Asignatura->setNombre_asignatura('test');
        $this->assertEquals('test', $this->Asignatura->getNombre_asignatura());
    }

    public function test_set_and_get_nombre_corto()
    {
        $nombre_cortoVo = new AsignaturaShortName('Test value');
        $this->Asignatura->setNombreCortoVo($nombre_cortoVo);
        $this->assertInstanceOf(AsignaturaShortName::class, $this->Asignatura->getNombreCortoVo());
        $this->assertEquals('Test value', $this->Asignatura->getNombreCortoVo()->value());
    }

    public function test_set_and_get_creditos()
    {
        $creditosVo = new Creditos(1);
        $this->Asignatura->setCreditosVo($creditosVo);
        $this->assertInstanceOf(Creditos::class, $this->Asignatura->getCreditosVo());
        $this->assertEquals(1, $this->Asignatura->getCreditosVo()->value());
    }

    public function test_set_and_get_year()
    {
        $yearVo = new YearText('2');
        $this->Asignatura->setYearVo($yearVo);
        $this->assertInstanceOf(YearText::class, $this->Asignatura->getYearVo());
        $this->assertEquals('2', $this->Asignatura->getYearVo()->value());
    }

    public function test_set_and_get_id_sector()
    {
        $id_sectorVo = new SectorId(1);
        $this->Asignatura->setIdSectorVo($id_sectorVo);
        $this->assertInstanceOf(SectorId::class, $this->Asignatura->getIdSectorVo());
        $this->assertEquals(1, $this->Asignatura->getIdSectorVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->Asignatura->setActive(true);
        $this->assertTrue($this->Asignatura->isActive());
    }

    public function test_set_and_get_id_tipo()
    {
        $id_tipoVo = new AsignaturaTipoId(5);
        $this->Asignatura->setIdTipoVo($id_tipoVo);
        $this->assertInstanceOf(AsignaturaTipoId::class, $this->Asignatura->getIdTipoVo());
        $this->assertEquals(5, $this->Asignatura->getIdTipoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $asignatura = new Asignatura();
        $attributes = [
            'id_asignatura' => new AsignaturaId(1001),
            'id_nivel' => new NivelId(1001),
            'nombre_signatura' => 'test',
            'nombre_corto' => new AsignaturaShortName('Test value'),
            'creditos' => new Creditos(1),
            'year' => new YearText('2'),
            'id_sector' => new SectorId(1),
            'active' => true,
            'id_tipo' => new AsignaturaTipoId(5),
        ];
        $asignatura->setAllAttributes($attributes);

        $this->assertEquals(1001, $asignatura->getIdAsignaturaVo()->value());
        $this->assertEquals(1001, $asignatura->getIdNivelVo()->value());
        $this->assertEquals('test', $asignatura->getNombre_asignatura());
        $this->assertEquals('Test value', $asignatura->getNombreCortoVo()->value());
        $this->assertEquals(1, $asignatura->getCreditosVo()->value());
        $this->assertEquals('2', $asignatura->getYearVo()->value());
        $this->assertEquals(1, $asignatura->getIdSectorVo()->value());
        $this->assertTrue($asignatura->isActive());
        $this->assertEquals(5, $asignatura->getIdTipoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $asignatura = new Asignatura();
        $attributes = [
            'id_asignatura' => 1001,
            'id_nivel' => 1001,
            'nombre_signatura' => 'test',
            'nombre_corto' => 'Test value',
            'creditos' => 1,
            'year' => '2',
            'id_sector' => 1,
            'active' => true,
            'id_tipo' => 5,
        ];
        $asignatura->setAllAttributes($attributes);

        $this->assertEquals(1001, $asignatura->getIdAsignaturaVo()->value());
        $this->assertEquals(1001, $asignatura->getIdNivelVo()->value());
        $this->assertEquals('test', $asignatura->getNombre_asignatura());
        $this->assertEquals('Test value', $asignatura->getNombreCortoVo()->value());
        $this->assertEquals(1, $asignatura->getCreditosVo()->value());
        $this->assertEquals('2', $asignatura->getYearVo()->value());
        $this->assertEquals(1, $asignatura->getIdSectorVo()->value());
        $this->assertTrue($asignatura->isActive());
        $this->assertEquals(5, $asignatura->getIdTipoVo()->value());
    }
}
