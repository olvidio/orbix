<?php

namespace Tests\unit\ubis\domain\entity;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\value_objects\BibliotecaText;
use src\ubis\domain\value_objects\CasaId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\NumSacerdotes;
use src\ubis\domain\value_objects\ObservCasaText;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\Plazas;
use src\ubis\domain\value_objects\PlazasMin;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCasaText;
use src\ubis\domain\value_objects\UbiNombreText;
use Tests\myTest;

class CasaTest extends myTest
{
    private Casa $Casa;

    public function setUp(): void
    {
        parent::setUp();
        $this->Casa = new Casa();
        $this->Casa->setIdUbiVo(new CasaId(1));
        $this->Casa->setNombreUbiVo(new UbiNombreText('Test'));
    }

    public function test_set_and_get_tipo_ubi()
    {
        $this->Casa->setTipo_ubi('test');
        $this->assertEquals('test', $this->Casa->getTipo_ubi());
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new CasaId(1);
        $this->Casa->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(CasaId::class, $this->Casa->getIdUbiVo());
        $this->assertEquals(1, $this->Casa->getIdUbiVo()->value());
    }

    public function test_set_and_get_nombre_ubi()
    {
        $nombre_ubiVo = new UbiNombreText('Test');
        $this->Casa->setNombreUbiVo($nombre_ubiVo);
        $this->assertInstanceOf(UbiNombreText::class, $this->Casa->getNombreUbiVo());
        $this->assertEquals('Test', $this->Casa->getNombreUbiVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('TST');
        $this->Casa->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->Casa->getDlVo());
        $this->assertEquals('TST', $this->Casa->getDlVo()->value());
    }

    public function test_set_and_get_pais()
    {
        $paisVo = new PaisName('Spain');
        $this->Casa->setPaisVo($paisVo);
        $this->assertInstanceOf(PaisName::class, $this->Casa->getPaisVo());
        $this->assertEquals('Spain', $this->Casa->getPaisVo()->value());
    }

    public function test_set_and_get_region()
    {
        $regionVo = new RegionNameText('Test');
        $this->Casa->setRegionVo($regionVo);
        $this->assertInstanceOf(RegionNameText::class, $this->Casa->getRegionVo());
        $this->assertEquals('Test', $this->Casa->getRegionVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->Casa->setActive(true);
        $this->assertTrue($this->Casa->isActive());
    }

    public function test_set_and_get_f_active()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Casa->setF_active($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Casa->getF_active());
        $this->assertEquals('2024-01-15 10:30:00', $this->Casa->getF_active()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_tipo_casa()
    {
        $tipo_casaVo = new TipoCasaText('Test');
        $this->Casa->setTipoCasaVo($tipo_casaVo);
        $this->assertInstanceOf(TipoCasaText::class, $this->Casa->getTipoCasaVo());
        $this->assertEquals('Test', $this->Casa->getTipoCasaVo()->value());
    }

    public function test_set_and_get_plazas()
    {
        $plazasVo = new Plazas(1);
        $this->Casa->setPlazasVo($plazasVo);
        $this->assertInstanceOf(Plazas::class, $this->Casa->getPlazasVo());
        $this->assertEquals(1, $this->Casa->getPlazasVo()->value());
    }

    public function test_set_and_get_plazas_min()
    {
        $plazas_minVo = new PlazasMin(1);
        $this->Casa->setPlazasMinVo($plazas_minVo);
        $this->assertInstanceOf(PlazasMin::class, $this->Casa->getPlazasMinVo());
        $this->assertEquals(1, $this->Casa->getPlazasMinVo()->value());
    }

    public function test_set_and_get_num_sacd()
    {
        $num_sacdVo = new NumSacerdotes(1);
        $this->Casa->setNumSacdVo($num_sacdVo);
        $this->assertInstanceOf(NumSacerdotes::class, $this->Casa->getNumSacdVo());
        $this->assertEquals(1, $this->Casa->getNumSacdVo()->value());
    }

    public function test_set_and_get_biblioteca()
    {
        $bibliotecaVo = new BibliotecaText('Test');
        $this->Casa->setBibliotecaVo($bibliotecaVo);
        $this->assertInstanceOf(BibliotecaText::class, $this->Casa->getBibliotecaVo());
        $this->assertEquals('Test', $this->Casa->getBibliotecaVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservCasaText('Test');
        $this->Casa->setObservVo($observVo);
        $this->assertInstanceOf(ObservCasaText::class, $this->Casa->getObservVo());
        $this->assertEquals('Test', $this->Casa->getObservVo()->value());
    }

    public function test_set_all_attributes()
    {
        $casa = new Casa();
        $attributes = [
            'tipo_ubi' => 'test',
            'id_ubi' => new CasaId(1),
            'nombre_ubi' => new UbiNombreText('Test'),
            'dl' => new DelegacionCode('TST'),
            'pais' => new PaisName('Spain'),
            'region' => new RegionNameText('Test'),
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo_casa' => new TipoCasaText('Test'),
            'plazas' => new Plazas(1),
            'plazas_min' => new PlazasMin(1),
            'num_sacd' => new NumSacerdotes(1),
            'biblioteca' => new BibliotecaText('Test'),
            'observ' => new ObservCasaText('Test'),
        ];
        $casa->setAllAttributes($attributes);

        $this->assertEquals('test', $casa->getTipo_ubi());
        $this->assertEquals(1, $casa->getIdUbiVo()->value());
        $this->assertEquals('Test', $casa->getNombreUbiVo()->value());
        $this->assertEquals('TST', $casa->getDlVo()->value());
        $this->assertEquals('Spain', $casa->getPaisVo()->value());
        $this->assertEquals('Test', $casa->getRegionVo()->value());
        $this->assertTrue($casa->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $casa->getF_active()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test', $casa->getTipoCasaVo()->value());
        $this->assertEquals(1, $casa->getPlazasVo()->value());
        $this->assertEquals(1, $casa->getPlazasMinVo()->value());
        $this->assertEquals(1, $casa->getNumSacdVo()->value());
        $this->assertEquals('Test', $casa->getBibliotecaVo()->value());
        $this->assertEquals('Test', $casa->getObservVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $casa = new Casa();
        $attributes = [
            'tipo_ubi' => 'test',
            'id_ubi' => 1,
            'nombre_ubi' => 'Test',
            'dl' => 'TST',
            'pais' => 'Spain',
            'region' => 'Test',
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo_casa' => 'Test',
            'plazas' => 1,
            'plazas_min' => 1,
            'num_sacd' => 1,
            'biblioteca' => 'Test',
            'observ' => 'Test',
        ];
        $casa->setAllAttributes($attributes);

        $this->assertEquals('test', $casa->getTipo_ubi());
        $this->assertEquals(1, $casa->getIdUbiVo()->value());
        $this->assertEquals('Test', $casa->getNombreUbiVo()->value());
        $this->assertEquals('TST', $casa->getDlVo()->value());
        $this->assertEquals('Spain', $casa->getPaisVo()->value());
        $this->assertEquals('Test', $casa->getRegionVo()->value());
        $this->assertTrue($casa->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $casa->getF_active()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test', $casa->getTipoCasaVo()->value());
        $this->assertEquals(1, $casa->getPlazasVo()->value());
        $this->assertEquals(1, $casa->getPlazasMinVo()->value());
        $this->assertEquals(1, $casa->getNumSacdVo()->value());
        $this->assertEquals('Test', $casa->getBibliotecaVo()->value());
        $this->assertEquals('Test', $casa->getObservVo()->value());
    }
}
