<?php

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\Region;
use src\ubis\domain\value_objects\RegionCode;
use src\ubis\domain\value_objects\RegionId;
use src\ubis\domain\value_objects\RegionName;
use src\ubis\domain\value_objects\RegionNameText;
use Tests\myTest;

class RegionTest extends myTest
{
    private Region $Region;

    public function setUp(): void
    {
        parent::setUp();
        $this->Region = new Region();
        $this->Region->setId_region(1);
        $this->Region->setRegionVo(new RegionCode('TST'));
    }

    public function test_get_id_region()
    {
        $this->assertEquals(1, $this->Region->getId_region());
    }

    public function test_set_and_get_region()
    {
        $regionVo = new RegionCode('TST');
        $this->Region->setRegionVo($regionVo);
        $this->assertInstanceOf(RegionCode::class, $this->Region->getRegionVo());
        $this->assertEquals('TST', $this->Region->getRegionVo()->value());
    }

    public function test_set_and_get_nombre_region()
    {
        $nombre_regionVo = new RegionNameText('Test');
        $this->Region->setNombreRegionVo($nombre_regionVo);
        $this->assertInstanceOf(RegionNameText::class, $this->Region->getNombreRegionVo());
        $this->assertEquals('Test', $this->Region->getNombreRegionVo()->value());
    }

    public function test_set_all_attributes()
    {
        $region = new Region();
        $attributes = [
            'id_region' => new RegionId(1),
            'region' => new RegionCode('TST'),
            'nombre_region' => new RegionNameText('Test'),
        ];
        $region->setAllAttributes($attributes);

        $this->assertEquals(1, $region->getIdRegionVo()->value());
        $this->assertEquals('TST', $region->getRegionVo()->value());
        $this->assertEquals('Test', $region->getNombreRegionVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $region = new Region();
        $attributes = [
            'id_region' => 1,
            'region' => 'TST',
            'nombre_region' => 'Test',
        ];
        $region->setAllAttributes($attributes);

        $this->assertEquals(1, $region->getIdRegionVo()->value());
        $this->assertEquals('TST', $region->getRegionVo()->value());
        $this->assertEquals('Test', $region->getNombreRegionVo()->value());
    }
}
