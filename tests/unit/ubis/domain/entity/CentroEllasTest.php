<?php

namespace Tests\unit\ubis\domain\entity;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\UbiNombreText;
use Tests\myTest;

class CentroEllasTest extends myTest
{
    private CentroEllas $CentroEllas;

    public function setUp(): void
    {
        parent::setUp();
        $this->CentroEllas = new CentroEllas();
        $this->CentroEllas->setIdUbiVo(new CentroId(1));
        $this->CentroEllas->setNombreUbiVo(new UbiNombreText('Test'));
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new CentroId(1);
        $this->CentroEllas->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(CentroId::class, $this->CentroEllas->getIdUbiVo());
        $this->assertEquals(1, $this->CentroEllas->getIdUbiVo()->value());
    }

    public function test_set_and_get_tipo_ubi()
    {
        $this->CentroEllas->setTipo_ubi('test');
        $this->assertEquals('test', $this->CentroEllas->getTipo_ubi());
    }

    public function test_set_and_get_nombre_ubi()
    {
        $nombre_ubiVo = new UbiNombreText('Test');
        $this->CentroEllas->setNombreUbiVo($nombre_ubiVo);
        $this->assertInstanceOf(UbiNombreText::class, $this->CentroEllas->getNombreUbiVo());
        $this->assertEquals('Test', $this->CentroEllas->getNombreUbiVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('TST');
        $this->CentroEllas->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->CentroEllas->getDlVo());
        $this->assertEquals('TST', $this->CentroEllas->getDlVo()->value());
    }

    public function test_set_and_get_pais()
    {
        $paisVo = new PaisName('Spain');
        $this->CentroEllas->setPaisVo($paisVo);
        $this->assertInstanceOf(PaisName::class, $this->CentroEllas->getPaisVo());
        $this->assertEquals('Spain', $this->CentroEllas->getPaisVo()->value());
    }

    public function test_set_and_get_region()
    {
        $regionVo = new RegionNameText('Test');
        $this->CentroEllas->setRegionVo($regionVo);
        $this->assertInstanceOf(RegionNameText::class, $this->CentroEllas->getRegionVo());
        $this->assertEquals('Test', $this->CentroEllas->getRegionVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->CentroEllas->setActive(true);
        $this->assertTrue($this->CentroEllas->isActive());
    }

    public function test_set_and_get_f_active()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->CentroEllas->setF_active($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->CentroEllas->getF_active());
        $this->assertEquals('2024-01-15 10:30:00', $this->CentroEllas->getF_active()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_sv()
    {
        $this->CentroEllas->setSv(true);
        $this->assertTrue($this->CentroEllas->isSv());
    }

    public function test_set_and_get_sf()
    {
        $this->CentroEllas->setSf(true);
        $this->assertTrue($this->CentroEllas->isSf());
    }

    public function test_set_and_get_tipo_ctr()
    {
        $tipo_ctrVo = new TipoCentroCode('TST');
        $this->CentroEllas->setTipoCtrVo($tipo_ctrVo);
        $this->assertInstanceOf(TipoCentroCode::class, $this->CentroEllas->getTipoCtrVo());
        $this->assertEquals('TST', $this->CentroEllas->getTipoCtrVo()->value());
    }

    public function test_set_and_get_tipo_labor()
    {
        $tipo_laborVo = new TipoLaborId(1);
        $this->CentroEllas->setTipoLaborVo($tipo_laborVo);
        $this->assertInstanceOf(TipoLaborId::class, $this->CentroEllas->getTipoLaborVo());
        $this->assertEquals(1, $this->CentroEllas->getTipoLaborVo()->value());
    }

    public function test_set_and_get_cdc()
    {
        $this->CentroEllas->setCdc(true);
        $this->assertTrue($this->CentroEllas->isCdc());
    }

    public function test_set_and_get_id_ctr_padre()
    {
        $id_ctr_padreVo = new CentroId(1);
        $this->CentroEllas->setIdCtrPadreVo($id_ctr_padreVo);
        $this->assertInstanceOf(CentroId::class, $this->CentroEllas->getIdCtrPadreVo());
        $this->assertEquals(1, $this->CentroEllas->getIdCtrPadreVo()->value());
    }

    public function test_set_and_get_id_zona()
    {
        $this->CentroEllas->setId_zona(1);
        $this->assertEquals(1, $this->CentroEllas->getId_zona());
    }

    public function test_set_all_attributes()
    {
        $centroEllas = new CentroEllas();
        $attributes = [
            'id_ubi' => new CentroId(1),
            'tipo_ubi' => 'test',
            'nombre_ubi' => new UbiNombreText('Test'),
            'dl' => new DelegacionCode('TST'),
            'pais' => new PaisName('Spain'),
            'region' => new RegionNameText('Test'),
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
            'sv' => true,
            'sf' => true,
            'tipo_ctr' => new TipoCentroCode('TST'),
            'tipo_labor' => new TipoLaborId(1),
            'cdc' => true,
            'id_ctr_padre' => new CentroId(1),
            'id_zona' => 1,
        ];
        $centroEllas->setAllAttributes($attributes);

        $this->assertEquals(1, $centroEllas->getIdUbiVo()->value());
        $this->assertEquals('test', $centroEllas->getTipo_ubi());
        $this->assertEquals('Test', $centroEllas->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centroEllas->getDlVo()->value());
        $this->assertEquals('Spain', $centroEllas->getPaisVo()->value());
        $this->assertEquals('Test', $centroEllas->getRegionVo()->value());
        $this->assertTrue($centroEllas->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centroEllas->getF_active()->format('Y-m-d H:i:s'));
        $this->assertTrue($centroEllas->isSv());
        $this->assertTrue($centroEllas->isSf());
        $this->assertEquals('TST', $centroEllas->getTipoCtrVo()->value());
        $this->assertEquals(1, $centroEllas->getTipoLaborVo()->value());
        $this->assertTrue($centroEllas->isCdc());
        $this->assertEquals(1, $centroEllas->getIdCtrPadreVo()->value());
        $this->assertEquals(1, $centroEllas->getId_zona());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $centroEllas = new CentroEllas();
        $attributes = [
            'id_ubi' => 1,
            'tipo_ubi' => 'test',
            'nombre_ubi' => 'Test',
            'dl' => 'TST',
            'pais' => 'Spain',
            'region' => 'Test',
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
            'sv' => true,
            'sf' => true,
            'tipo_ctr' => 'TST',
            'tipo_labor' => 1,
            'cdc' => true,
            'id_ctr_padre' => 1,
            'id_zona' => 1,
        ];
        $centroEllas->setAllAttributes($attributes);

        $this->assertEquals(1, $centroEllas->getIdUbiVo()->value());
        $this->assertEquals('test', $centroEllas->getTipo_ubi());
        $this->assertEquals('Test', $centroEllas->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centroEllas->getDlVo()->value());
        $this->assertEquals('Spain', $centroEllas->getPaisVo()->value());
        $this->assertEquals('Test', $centroEllas->getRegionVo()->value());
        $this->assertTrue($centroEllas->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centroEllas->getF_active()->format('Y-m-d H:i:s'));
        $this->assertTrue($centroEllas->isSv());
        $this->assertTrue($centroEllas->isSf());
        $this->assertEquals('TST', $centroEllas->getTipoCtrVo()->value());
        $this->assertEquals(1, $centroEllas->getTipoLaborVo()->value());
        $this->assertTrue($centroEllas->isCdc());
        $this->assertEquals(1, $centroEllas->getIdCtrPadreVo()->value());
        $this->assertEquals(1, $centroEllas->getId_zona());
    }
}
