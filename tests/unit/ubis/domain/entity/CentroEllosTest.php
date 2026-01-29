<?php

namespace Tests\unit\ubis\domain\entity;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\CentroEllos;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\UbiNombreText;
use Tests\myTest;

class CentroEllosTest extends myTest
{
    private CentroEllos $CentroEllos;

    public function setUp(): void
    {
        parent::setUp();
        $this->CentroEllos = new CentroEllos();
        $this->CentroEllos->setIdUbiVo(new CentroId(1));
        $this->CentroEllos->setNombreUbiVo(new UbiNombreText('Test'));
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new CentroId(1);
        $this->CentroEllos->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(CentroId::class, $this->CentroEllos->getIdUbiVo());
        $this->assertEquals(1, $this->CentroEllos->getIdUbiVo()->value());
    }

    public function test_set_and_get_tipo_ubi()
    {
        $this->CentroEllos->setTipo_ubi('test');
        $this->assertEquals('test', $this->CentroEllos->getTipo_ubi());
    }

    public function test_set_and_get_nombre_ubi()
    {
        $nombre_ubiVo = new UbiNombreText('Test');
        $this->CentroEllos->setNombreUbiVo($nombre_ubiVo);
        $this->assertInstanceOf(UbiNombreText::class, $this->CentroEllos->getNombreUbiVo());
        $this->assertEquals('Test', $this->CentroEllos->getNombreUbiVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('TST');
        $this->CentroEllos->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->CentroEllos->getDlVo());
        $this->assertEquals('TST', $this->CentroEllos->getDlVo()->value());
    }

    public function test_set_and_get_pais()
    {
        $paisVo = new PaisName('Spain');
        $this->CentroEllos->setPaisVo($paisVo);
        $this->assertInstanceOf(PaisName::class, $this->CentroEllos->getPaisVo());
        $this->assertEquals('Spain', $this->CentroEllos->getPaisVo()->value());
    }

    public function test_set_and_get_region()
    {
        $regionVo = new RegionNameText('Test');
        $this->CentroEllos->setRegionVo($regionVo);
        $this->assertInstanceOf(RegionNameText::class, $this->CentroEllos->getRegionVo());
        $this->assertEquals('Test', $this->CentroEllos->getRegionVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->CentroEllos->setActive(true);
        $this->assertTrue($this->CentroEllos->isActive());
    }

    public function test_set_and_get_f_active()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->CentroEllos->setF_active($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->CentroEllos->getF_active());
        $this->assertEquals('2024-01-15 10:30:00', $this->CentroEllos->getF_active()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_sv()
    {
        $this->CentroEllos->setSv(true);
        $this->assertTrue($this->CentroEllos->isSv());
    }

    public function test_set_and_get_sf()
    {
        $this->CentroEllos->setSf(true);
        $this->assertTrue($this->CentroEllos->isSf());
    }

    public function test_set_and_get_tipo_ctr()
    {
        $tipo_ctrVo = new TipoCentroCode('TST');
        $this->CentroEllos->setTipoCtrVo($tipo_ctrVo);
        $this->assertInstanceOf(TipoCentroCode::class, $this->CentroEllos->getTipoCtrVo());
        $this->assertEquals('TST', $this->CentroEllos->getTipoCtrVo()->value());
    }

    public function test_set_and_get_tipo_labor()
    {
        $tipo_laborVo = new TipoLaborId(1);
        $this->CentroEllos->setTipoLaborVo($tipo_laborVo);
        $this->assertInstanceOf(TipoLaborId::class, $this->CentroEllos->getTipoLaborVo());
        $this->assertEquals(1, $this->CentroEllos->getTipoLaborVo()->value());
    }

    public function test_set_and_get_cdc()
    {
        $this->CentroEllos->setCdc(true);
        $this->assertTrue($this->CentroEllos->isCdc());
    }

    public function test_set_and_get_id_ctr_padre()
    {
        $id_ctr_padreVo = new CentroId(1);
        $this->CentroEllos->setIdCtrPadreVo($id_ctr_padreVo);
        $this->assertInstanceOf(CentroId::class, $this->CentroEllos->getIdCtrPadreVo());
        $this->assertEquals(1, $this->CentroEllos->getIdCtrPadreVo()->value());
    }

    public function test_set_and_get_id_zona()
    {
        $this->CentroEllos->setId_zona(1);
        $this->assertEquals(1, $this->CentroEllos->getId_zona());
    }

    public function test_set_all_attributes()
    {
        $centroEllos = new CentroEllos();
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
        $centroEllos->setAllAttributes($attributes);

        $this->assertEquals(1, $centroEllos->getIdUbiVo()->value());
        $this->assertEquals('test', $centroEllos->getTipo_ubi());
        $this->assertEquals('Test', $centroEllos->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centroEllos->getDlVo()->value());
        $this->assertEquals('Spain', $centroEllos->getPaisVo()->value());
        $this->assertEquals('Test', $centroEllos->getRegionVo()->value());
        $this->assertTrue($centroEllos->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centroEllos->getF_active()->format('Y-m-d H:i:s'));
        $this->assertTrue($centroEllos->isSv());
        $this->assertTrue($centroEllos->isSf());
        $this->assertEquals('TST', $centroEllos->getTipoCtrVo()->value());
        $this->assertEquals(1, $centroEllos->getTipoLaborVo()->value());
        $this->assertTrue($centroEllos->isCdc());
        $this->assertEquals(1, $centroEllos->getIdCtrPadreVo()->value());
        $this->assertEquals(1, $centroEllos->getId_zona());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $centroEllos = new CentroEllos();
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
        $centroEllos->setAllAttributes($attributes);

        $this->assertEquals(1, $centroEllos->getIdUbiVo()->value());
        $this->assertEquals('test', $centroEllos->getTipo_ubi());
        $this->assertEquals('Test', $centroEllos->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centroEllos->getDlVo()->value());
        $this->assertEquals('Spain', $centroEllos->getPaisVo()->value());
        $this->assertEquals('Test', $centroEllos->getRegionVo()->value());
        $this->assertTrue($centroEllos->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centroEllos->getF_active()->format('Y-m-d H:i:s'));
        $this->assertTrue($centroEllos->isSv());
        $this->assertTrue($centroEllos->isSf());
        $this->assertEquals('TST', $centroEllos->getTipoCtrVo()->value());
        $this->assertEquals(1, $centroEllos->getTipoLaborVo()->value());
        $this->assertTrue($centroEllos->isCdc());
        $this->assertEquals(1, $centroEllos->getIdCtrPadreVo()->value());
        $this->assertEquals(1, $centroEllos->getId_zona());
    }
}
