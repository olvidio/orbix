<?php

namespace Tests\unit\ubis\domain\entity;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\UbiNombreText;
use Tests\myTest;

class CentroTest extends myTest
{
    private Centro $Centro;

    public function setUp(): void
    {
        parent::setUp();
        $this->Centro = new Centro();
        $this->Centro->setIdUbiVo(new CentroId(1));
        $this->Centro->setNombreUbiVo(new UbiNombreText('Test'));
    }

    public function test_set_and_get_tipo_ubi()
    {
        $this->Centro->setTipo_ubi('test');
        $this->assertEquals('test', $this->Centro->getTipo_ubi());
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new CentroId(1);
        $this->Centro->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(CentroId::class, $this->Centro->getIdUbiVo());
        $this->assertEquals(1, $this->Centro->getIdUbiVo()->value());
    }

    public function test_set_and_get_nombre_ubi()
    {
        $nombre_ubiVo = new UbiNombreText('Test');
        $this->Centro->setNombreUbiVo($nombre_ubiVo);
        $this->assertInstanceOf(UbiNombreText::class, $this->Centro->getNombreUbiVo());
        $this->assertEquals('Test', $this->Centro->getNombreUbiVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('TST');
        $this->Centro->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->Centro->getDlVo());
        $this->assertEquals('TST', $this->Centro->getDlVo()->value());
    }

    public function test_set_and_get_pais()
    {
        $paisVo = new PaisName('Spain');
        $this->Centro->setPaisVo($paisVo);
        $this->assertInstanceOf(PaisName::class, $this->Centro->getPaisVo());
        $this->assertEquals('Spain', $this->Centro->getPaisVo()->value());
    }

    public function test_set_and_get_region()
    {
        $regionVo = new RegionNameText('Test');
        $this->Centro->setRegionVo($regionVo);
        $this->assertInstanceOf(RegionNameText::class, $this->Centro->getRegionVo());
        $this->assertEquals('Test', $this->Centro->getRegionVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->Centro->setActive(true);
        $this->assertTrue($this->Centro->isActive());
    }

    public function test_set_and_get_f_active()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Centro->setF_active($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Centro->getF_active());
        $this->assertEquals('2024-01-15 10:30:00', $this->Centro->getF_active()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_tipo_ctr()
    {
        $tipo_ctrVo = new TipoCentroCode('TST');
        $this->Centro->setTipoCtrVo($tipo_ctrVo);
        $this->assertInstanceOf(TipoCentroCode::class, $this->Centro->getTipoCtrVo());
        $this->assertEquals('TST', $this->Centro->getTipoCtrVo()->value());
    }

    public function test_set_and_get_tipo_labor()
    {
        $tipo_laborVo = new TipoLaborId(1);
        $this->Centro->setTipoLaborVo($tipo_laborVo);
        $this->assertInstanceOf(TipoLaborId::class, $this->Centro->getTipoLaborVo());
        $this->assertEquals(1, $this->Centro->getTipoLaborVo()->value());
    }

    public function test_set_and_get_id_ctr_padre()
    {
        $id_ctr_padreVo = new CentroId(1);
        $this->Centro->setIdCtrPadreVo($id_ctr_padreVo);
        $this->assertInstanceOf(CentroId::class, $this->Centro->getIdCtrPadreVo());
        $this->assertEquals(1, $this->Centro->getIdCtrPadreVo()->value());
    }

    public function test_set_all_attributes()
    {
        $centro = new Centro();
        $attributes = [
            'tipo_ubi' => 'test',
            'id_ubi' => new CentroId(1),
            'nombre_ubi' => new UbiNombreText('Test'),
            'dl' => new DelegacionCode('TST'),
            'pais' => new PaisName('Spain'),
            'region' => new RegionNameText('Test'),
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo_ctr' => new TipoCentroCode('TST'),
            'tipo_labor' => new TipoLaborId(1),
            'id_ctr_padre' => new CentroId(1),
        ];
        $centro->setAllAttributes($attributes);

        $this->assertEquals('test', $centro->getTipo_ubi());
        $this->assertEquals(1, $centro->getIdUbiVo()->value());
        $this->assertEquals('Test', $centro->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centro->getDlVo()->value());
        $this->assertEquals('Spain', $centro->getPaisVo()->value());
        $this->assertEquals('Test', $centro->getRegionVo()->value());
        $this->assertTrue($centro->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centro->getF_active()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $centro->getTipoCtrVo()->value());
        $this->assertEquals(1, $centro->getTipoLaborVo()->value());
        $this->assertEquals(1, $centro->getIdCtrPadreVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $centro = new Centro();
        $attributes = [
            'tipo_ubi' => 'test',
            'id_ubi' => 1,
            'nombre_ubi' => 'Test',
            'dl' => 'TST',
            'pais' => 'Spain',
            'region' => 'Test',
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo_ctr' => 'TST',
            'tipo_labor' => 1,
            'id_ctr_padre' => 1,
        ];
        $centro->setAllAttributes($attributes);

        $this->assertEquals('test', $centro->getTipo_ubi());
        $this->assertEquals(1, $centro->getIdUbiVo()->value());
        $this->assertEquals('Test', $centro->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centro->getDlVo()->value());
        $this->assertEquals('Spain', $centro->getPaisVo()->value());
        $this->assertEquals('Test', $centro->getRegionVo()->value());
        $this->assertTrue($centro->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centro->getF_active()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $centro->getTipoCtrVo()->value());
        $this->assertEquals(1, $centro->getTipoLaborVo()->value());
        $this->assertEquals(1, $centro->getIdCtrPadreVo()->value());
    }
}
