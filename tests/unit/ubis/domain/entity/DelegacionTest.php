<?php

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\DelegacionGrupoEstudios;
use src\ubis\domain\value_objects\DelegacionId;
use src\ubis\domain\value_objects\DelegacionName;
use src\ubis\domain\value_objects\DelegacionRegionStgr;
use src\ubis\domain\value_objects\RegionCode;
use Tests\myTest;

class DelegacionTest extends myTest
{
    private Delegacion $Delegacion;

    public function setUp(): void
    {
        parent::setUp();
        $this->Delegacion = new Delegacion();
        $this->Delegacion->setIdDlVo(new DelegacionId(1));
        $this->Delegacion->setDlVo(new DelegacionCode('TST'));
    }

    public function test_set_and_get_id_dl()
    {
        $id_dlVo = new DelegacionId(1);
        $this->Delegacion->setIdDlVo($id_dlVo);
        $this->assertInstanceOf(DelegacionId::class, $this->Delegacion->getIdDlVo());
        $this->assertEquals(1, $this->Delegacion->getIdDlVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('TST');
        $this->Delegacion->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->Delegacion->getDlVo());
        $this->assertEquals('TST', $this->Delegacion->getDlVo()->value());
    }

    public function test_set_and_get_region()
    {
        $regionVo = new RegionCode('TST');
        $this->Delegacion->setRegionVo($regionVo);
        $this->assertInstanceOf(RegionCode::class, $this->Delegacion->getRegionVo());
        $this->assertEquals('TST', $this->Delegacion->getRegionVo()->value());
    }

    public function test_set_and_get_nombre_dl()
    {
        $nombre_dlVo = new DelegacionName('TST');
        $this->Delegacion->setNombreDlVo($nombre_dlVo);
        $this->assertInstanceOf(DelegacionName::class, $this->Delegacion->getNombreDlVo());
        $this->assertEquals('TST', $this->Delegacion->getNombreDlVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->Delegacion->setActive(true);
        $this->assertTrue($this->Delegacion->isActive());
    }

    public function test_set_and_get_grupo_estudios()
    {
        $grupo_estudiosVo = new DelegacionGrupoEstudios('TST');
        $this->Delegacion->setGrupoEstudiosVo($grupo_estudiosVo);
        $this->assertInstanceOf(DelegacionGrupoEstudios::class, $this->Delegacion->getGrupoEstudiosVo());
        $this->assertEquals('TST', $this->Delegacion->getGrupoEstudiosVo()->value());
    }

    public function test_set_and_get_region_stgr()
    {
        $region_stgrVo = new DelegacionRegionStgr('TST');
        $this->Delegacion->setRegionStgrVo($region_stgrVo);
        $this->assertInstanceOf(DelegacionRegionStgr::class, $this->Delegacion->getRegionStgrVo());
        $this->assertEquals('TST', $this->Delegacion->getRegionStgrVo()->value());
    }

    public function test_set_all_attributes()
    {
        $delegacion = new Delegacion();
        $attributes = [
            'id_dl' => new DelegacionId(1),
            'dl' => new DelegacionCode('TST'),
            'region' => new RegionCode('TST'),
            'nombre_dl' => new DelegacionName('TST'),
            'active' => true,
            'grupo_estudios' => new DelegacionGrupoEstudios('TST'),
            'region_stgr' => new DelegacionRegionStgr('TST'),
        ];
        $delegacion->setAllAttributes($attributes);

        $this->assertEquals(1, $delegacion->getIdDlVo()->value());
        $this->assertEquals('TST', $delegacion->getDlVo()->value());
        $this->assertEquals('TST', $delegacion->getRegionVo()->value());
        $this->assertEquals('TST', $delegacion->getNombreDlVo()->value());
        $this->assertTrue($delegacion->isActive());
        $this->assertEquals('TST', $delegacion->getGrupoEstudiosVo()->value());
        $this->assertEquals('TST', $delegacion->getRegionStgrVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $delegacion = new Delegacion();
        $attributes = [
            'id_dl' => 1,
            'dl' => 'TST',
            'region' => 'TST',
            'nombre_dl' => 'TST',
            'active' => true,
            'grupo_estudios' => 'TST',
            'region_stgr' => 'TST',
        ];
        $delegacion->setAllAttributes($attributes);

        $this->assertEquals(1, $delegacion->getIdDlVo()->value());
        $this->assertEquals('TST', $delegacion->getDlVo()->value());
        $this->assertEquals('TST', $delegacion->getRegionVo()->value());
        $this->assertEquals('TST', $delegacion->getNombreDlVo()->value());
        $this->assertTrue($delegacion->isActive());
        $this->assertEquals('TST', $delegacion->getGrupoEstudiosVo()->value());
        $this->assertEquals('TST', $delegacion->getRegionStgrVo()->value());
    }
}
