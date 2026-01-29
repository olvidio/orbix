<?php

namespace Tests\unit\actividadtarifas\domain\entity;

use src\actividadtarifas\domain\entity\TipoTarifa;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\actividadtarifas\domain\value_objects\TarifaLetraCode;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use src\shared\domain\value_objects\SfsvId;
use src\ubis\domain\value_objects\ObservCasaText;
use Tests\myTest;

class TipoTarifaTest extends myTest
{
    private TipoTarifa $TipoTarifa;

    public function setUp(): void
    {
        parent::setUp();
        $this->TipoTarifa = new TipoTarifa();
        $this->TipoTarifa->setIdTarifaVo(new TarifaId(1));
        $this->TipoTarifa->setModoVo(new TarifaModoId(1));
    }

    public function test_set_and_get_id_tarifa()
    {
        $id_tarifaVo = new TarifaId(1);
        $this->TipoTarifa->setIdTarifaVo($id_tarifaVo);
        $this->assertInstanceOf(TarifaId::class, $this->TipoTarifa->getIdTarifaVo());
        $this->assertEquals(1, $this->TipoTarifa->getIdTarifaVo()->value());
    }

    public function test_set_and_get_modo()
    {
        $modoVo = new TarifaModoId(1);
        $this->TipoTarifa->setModoVo($modoVo);
        $this->assertInstanceOf(TarifaModoId::class, $this->TipoTarifa->getModoVo());
        $this->assertEquals(1, $this->TipoTarifa->getModoVo()->value());
    }

    public function test_set_and_get_letra()
    {
        $letraVo = new TarifaLetraCode('Test');
        $this->TipoTarifa->setLetraVo($letraVo);
        $this->assertInstanceOf(TarifaLetraCode::class, $this->TipoTarifa->getLetraVo());
        $this->assertEquals('TEST', $this->TipoTarifa->getLetraVo()->value());
    }

    public function test_set_and_get_sfsv()
    {
        $sfsvVo = new SfsvId(1);
        $this->TipoTarifa->setSfsvVo($sfsvVo);
        $this->assertInstanceOf(SfsvId::class, $this->TipoTarifa->getSfsvVo());
        $this->assertEquals(1, $this->TipoTarifa->getSfsvVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservCasaText('Test value');
        $this->TipoTarifa->setObservVo($observVo);
        $this->assertInstanceOf(ObservCasaText::class, $this->TipoTarifa->getObservVo());
        $this->assertEquals('Test value', $this->TipoTarifa->getObservVo()->value());
    }

    public function test_set_all_attributes()
    {
        $tipoTarifa = new TipoTarifa();
        $attributes = [
            'id_tarifa' => new TarifaId(1),
            'modo' => new TarifaModoId(1),
            'letra' => new TarifaLetraCode('Test'),
            'sfsv' => new SfsvId(1),
            'observ' => new ObservCasaText('Test value'),
        ];
        $tipoTarifa->setAllAttributes($attributes);

        $this->assertEquals(1, $tipoTarifa->getIdTarifaVo()->value());
        $this->assertEquals(1, $tipoTarifa->getModoVo()->value());
        $this->assertEquals('TEST', $tipoTarifa->getLetraVo()->value());
        $this->assertEquals(1, $tipoTarifa->getSfsvVo()->value());
        $this->assertEquals('Test value', $tipoTarifa->getObservVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tipoTarifa = new TipoTarifa();
        $attributes = [
            'id_tarifa' => 1,
            'modo' => 1,
            'letra' => 'TEST',
            'sfsv' => 1,
            'observ' => 'Test value',
        ];
        $tipoTarifa->setAllAttributes($attributes);

        $this->assertEquals(1, $tipoTarifa->getIdTarifaVo()->value());
        $this->assertEquals(1, $tipoTarifa->getModoVo()->value());
        $this->assertEquals('TEST', $tipoTarifa->getLetraVo()->value());
        $this->assertEquals(1, $tipoTarifa->getSfsvVo()->value());
        $this->assertEquals('Test value', $tipoTarifa->getObservVo()->value());
    }
}
