<?php

namespace Tests\unit\ubis\domain\entity;

use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\profesores\domain\value_objects\YearNumber;
use src\ubis\domain\entity\TarifaUbi;
use src\ubis\domain\value_objects\ObservCasaText;
use src\ubis\domain\value_objects\TarifaCantidad;
use Tests\myTest;

class TarifaUbiTest extends myTest
{
    private TarifaUbi $TarifaUbi;

    public function setUp(): void
    {
        parent::setUp();
        $this->TarifaUbi = new TarifaUbi();
        $this->TarifaUbi->setId_item(1);
        $this->TarifaUbi->setId_ubi(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->TarifaUbi->setId_item(1);
        $this->assertEquals(1, $this->TarifaUbi->getId_item());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->TarifaUbi->setId_ubi(1);
        $this->assertEquals(1, $this->TarifaUbi->getId_ubi());
    }

    public function test_set_and_get_id_tarifa()
    {
        $id_tarifaVo = new TarifaId(1);
        $this->TarifaUbi->setIdTarifaVo($id_tarifaVo);
        $this->assertInstanceOf(TarifaId::class, $this->TarifaUbi->getIdTarifaVo());
        $this->assertEquals(1, $this->TarifaUbi->getIdTarifaVo()->value());
    }

    public function test_set_and_get_year()
    {
        $yearVo = new YearNumber(2025);
        $this->TarifaUbi->setYearVo($yearVo);
        $this->assertInstanceOf(YearNumber::class, $this->TarifaUbi->getYearVo());
        $this->assertEquals(2025, $this->TarifaUbi->getYearVo()->value());
    }

    public function test_set_and_get_cantidad()
    {
        $cantidadVo = new TarifaCantidad(1);
        $this->TarifaUbi->setCantidadVo($cantidadVo);
        $this->assertInstanceOf(TarifaCantidad::class, $this->TarifaUbi->getCantidadVo());
        $this->assertEquals(1, $this->TarifaUbi->getCantidadVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservCasaText('Test');
        $this->TarifaUbi->setObservVo($observVo);
        $this->assertInstanceOf(ObservCasaText::class, $this->TarifaUbi->getObservVo());
        $this->assertEquals('Test', $this->TarifaUbi->getObservVo()->value());
    }

    public function test_set_and_get_id_serie()
    {
        $id_serieVo = new serieId(1);
        $this->TarifaUbi->setIdSerieVo($id_serieVo);
        $this->assertInstanceOf(serieId::class, $this->TarifaUbi->getIdSerieVo());
        $this->assertEquals(1, $this->TarifaUbi->getIdSerieVo()->value());
    }

    public function test_set_all_attributes()
    {
        $tarifaUbi = new TarifaUbi();
        $attributes = [
            'id_item' => 1,
            'id_ubi' => 1,
            'id_tarifa' => new TarifaId(1),
            'year' => new YearNumber(2025),
            'cantidad' => new TarifaCantidad(1),
            'observ' => new ObservCasaText('Test'),
            'id_serie' => new serieId(1),
        ];
        $tarifaUbi->setAllAttributes($attributes);

        $this->assertEquals(1, $tarifaUbi->getId_item());
        $this->assertEquals(1, $tarifaUbi->getId_ubi());
        $this->assertEquals(1, $tarifaUbi->getIdTarifaVo()->value());
        $this->assertEquals(2025, $tarifaUbi->getYearVo()->value());
        $this->assertEquals(1, $tarifaUbi->getCantidadVo()->value());
        $this->assertEquals('Test', $tarifaUbi->getObservVo()->value());
        $this->assertEquals(1, $tarifaUbi->getIdSerieVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tarifaUbi = new TarifaUbi();
        $attributes = [
            'id_item' => 1,
            'id_ubi' => 1,
            'id_tarifa' => 1,
            'year' => 2026,
            'cantidad' => 1,
            'observ' => 'Test',
            'id_serie' => 1,
        ];
        $tarifaUbi->setAllAttributes($attributes);

        $this->assertEquals(1, $tarifaUbi->getId_item());
        $this->assertEquals(1, $tarifaUbi->getId_ubi());
        $this->assertEquals(1, $tarifaUbi->getIdTarifaVo()->value());
        $this->assertEquals(2026, $tarifaUbi->getYearVo()->value());
        $this->assertEquals(1, $tarifaUbi->getCantidadVo()->value());
        $this->assertEquals('Test', $tarifaUbi->getObservVo()->value());
        $this->assertEquals(1, $tarifaUbi->getIdSerieVo()->value());
    }
}
