<?php

namespace Tests\unit\asignaturas\domain\entity;

use src\asignaturas\domain\entity\Sector;
use src\asignaturas\domain\value_objects\DepartamentoId;
use src\asignaturas\domain\value_objects\SectorId;
use src\asignaturas\domain\value_objects\SectorName;
use Tests\myTest;

class SectorTest extends myTest
{
    private Sector $Sector;

    public function setUp(): void
    {
        parent::setUp();
        $this->Sector = new Sector();
        $this->Sector->setId_sector(1);
    }

    public function test_get_id_sector()
    {
        $this->assertEquals(1, $this->Sector->getId_sector());
    }

    public function test_set_and_get_id_departamento()
    {
        $id_departamentoVo = new DepartamentoId(1);
        $this->Sector->setIdDepartamentoVo($id_departamentoVo);
        $this->assertInstanceOf(DepartamentoId::class, $this->Sector->getIdDepartamentoVo());
        $this->assertEquals(1, $this->Sector->getIdDepartamentoVo()->value());
    }

    public function test_set_and_get_nombre_sector()
    {
        $nombre_sectorVo = new SectorName('Test value');
        $this->Sector->setNombreSectorVo($nombre_sectorVo);
        $this->assertInstanceOf(SectorName::class, $this->Sector->getNombreSectorVo());
        $this->assertEquals('Test value', $this->Sector->getNombreSectorVo()->value());
    }

    public function test_set_all_attributes()
    {
        $sector = new Sector();
        $attributes = [
            'id_sector' => new SectorId(1),
            'id_departamento' => new DepartamentoId(1),
            'nombre_sector' => new SectorName('Test value'),
        ];
        $sector->setAllAttributes($attributes);

        $this->assertEquals(1, $sector->getIdSectorVo()->value());
        $this->assertEquals(1, $sector->getIdDepartamentoVo()->value());
        $this->assertEquals('Test value', $sector->getNombreSectorVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $sector = new Sector();
        $attributes = [
            'id_sector' => 1,
            'id_departamento' => 1,
            'nombre_sector' => 'Test value',
        ];
        $sector->setAllAttributes($attributes);

        $this->assertEquals(1, $sector->getIdSectorVo()->value());
        $this->assertEquals(1, $sector->getIdDepartamentoVo()->value());
        $this->assertEquals('Test value', $sector->getNombreSectorVo()->value());
    }
}
