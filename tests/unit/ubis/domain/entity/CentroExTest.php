<?php

namespace Tests\unit\ubis\domain\entity;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\CentroEx;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\UbiNombreText;
use Tests\myTest;

class CentroExTest extends myTest
{
    private CentroEx $CentroEx;

    public function setUp(): void
    {
        parent::setUp();
        $this->CentroEx = new CentroEx();
        $this->CentroEx->setIdUbiVo(new CentroId(1));
        $this->CentroEx->setNombreUbiVo(new UbiNombreText('Test'));
    }

    public function test_set_and_get_tipo_ubi()
    {
        $this->CentroEx->setTipo_ubi('test');
        $this->assertEquals('test', $this->CentroEx->getTipo_ubi());
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new CentroId(1);
        $this->CentroEx->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(CentroId::class, $this->CentroEx->getIdUbiVo());
        $this->assertEquals(1, $this->CentroEx->getIdUbiVo()->value());
    }

    public function test_set_and_get_nombre_ubi()
    {
        $nombre_ubiVo = new UbiNombreText('Test');
        $this->CentroEx->setNombreUbiVo($nombre_ubiVo);
        $this->assertInstanceOf(UbiNombreText::class, $this->CentroEx->getNombreUbiVo());
        $this->assertEquals('Test', $this->CentroEx->getNombreUbiVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('TST');
        $this->CentroEx->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->CentroEx->getDlVo());
        $this->assertEquals('TST', $this->CentroEx->getDlVo()->value());
    }

    public function test_set_and_get_pais()
    {
        $paisVo = new PaisName('Spain');
        $this->CentroEx->setPaisVo($paisVo);
        $this->assertInstanceOf(PaisName::class, $this->CentroEx->getPaisVo());
        $this->assertEquals('Spain', $this->CentroEx->getPaisVo()->value());
    }

    public function test_set_and_get_region()
    {
        $regionVo = new RegionNameText('Test');
        $this->CentroEx->setRegionVo($regionVo);
        $this->assertInstanceOf(RegionNameText::class, $this->CentroEx->getRegionVo());
        $this->assertEquals('Test', $this->CentroEx->getRegionVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->CentroEx->setActive(true);
        $this->assertTrue($this->CentroEx->isActive());
    }

    public function test_set_and_get_f_active()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->CentroEx->setF_active($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->CentroEx->getF_active());
        $this->assertEquals('2024-01-15 10:30:00', $this->CentroEx->getF_active()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_tipo_ctr()
    {
        $tipo_ctrVo = new TipoCentroCode('TST');
        $this->CentroEx->setTipoCtrVo($tipo_ctrVo);
        $this->assertInstanceOf(TipoCentroCode::class, $this->CentroEx->getTipoCtrVo());
        $this->assertEquals('TST', $this->CentroEx->getTipoCtrVo()->value());
    }

    public function test_set_and_get_tipo_labor()
    {
        $tipo_laborVo = new TipoLaborId(1);
        $this->CentroEx->setTipoLaborVo($tipo_laborVo);
        $this->assertInstanceOf(TipoLaborId::class, $this->CentroEx->getTipoLaborVo());
        $this->assertEquals(1, $this->CentroEx->getTipoLaborVo()->value());
    }

    public function test_set_and_get_id_ctr_padre()
    {
        $id_ctr_padreVo = new CentroId(1);
        $this->CentroEx->setIdCtrPadreVo($id_ctr_padreVo);
        $this->assertInstanceOf(CentroId::class, $this->CentroEx->getIdCtrPadreVo());
        $this->assertEquals(1, $this->CentroEx->getIdCtrPadreVo()->value());
    }

    public function test_set_all_attributes()
    {
        $centroEx = new CentroEx();
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
        $centroEx->setAllAttributes($attributes);

        $this->assertEquals('test', $centroEx->getTipo_ubi());
        $this->assertEquals(1, $centroEx->getIdUbiVo()->value());
        $this->assertEquals('Test', $centroEx->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centroEx->getDlVo()->value());
        $this->assertEquals('Spain', $centroEx->getPaisVo()->value());
        $this->assertEquals('Test', $centroEx->getRegionVo()->value());
        $this->assertTrue($centroEx->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centroEx->getF_active()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $centroEx->getTipoCtrVo()->value());
        $this->assertEquals(1, $centroEx->getTipoLaborVo()->value());
        $this->assertEquals(1, $centroEx->getIdCtrPadreVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $centroEx = new CentroEx();
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
        $centroEx->setAllAttributes($attributes);

        $this->assertEquals('test', $centroEx->getTipo_ubi());
        $this->assertEquals(1, $centroEx->getIdUbiVo()->value());
        $this->assertEquals('Test', $centroEx->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centroEx->getDlVo()->value());
        $this->assertEquals('Spain', $centroEx->getPaisVo()->value());
        $this->assertEquals('Test', $centroEx->getRegionVo()->value());
        $this->assertTrue($centroEx->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centroEx->getF_active()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $centroEx->getTipoCtrVo()->value());
        $this->assertEquals(1, $centroEx->getTipoLaborVo()->value());
        $this->assertEquals(1, $centroEx->getIdCtrPadreVo()->value());
    }
}
