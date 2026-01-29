<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\Traslado;
use src\personas\domain\value_objects\NombreCentroText;
use src\personas\domain\value_objects\ObservText;
use src\personas\domain\value_objects\TrasladoTipoCmbCode;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class TrasladoTest extends myTest
{
    private Traslado $Traslado;

    public function setUp(): void
    {
        parent::setUp();
        $this->Traslado = new Traslado();
        $this->Traslado->setId_item(1);
        $this->Traslado->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->Traslado->setId_item(1);
        $this->assertEquals(1, $this->Traslado->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->Traslado->setId_nom(1);
        $this->assertEquals(1, $this->Traslado->getId_nom());
    }

    public function test_set_and_get_f_traslado()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Traslado->setF_traslado($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Traslado->getF_traslado());
        $this->assertEquals('2024-01-15 10:30:00', $this->Traslado->getF_traslado()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_tipo_cmb()
    {
        $tipo_cmbVo = new TrasladoTipoCmbCode('sede');
        $this->Traslado->setTipoCmbVo($tipo_cmbVo);
        $this->assertInstanceOf(TrasladoTipoCmbCode::class, $this->Traslado->getTipoCmbVo());
        $this->assertEquals('sede', $this->Traslado->getTipoCmbVo()->value());
    }

    public function test_set_and_get_id_ctr_origen()
    {
        $this->Traslado->setId_ctr_origen(1);
        $this->assertEquals(1, $this->Traslado->getId_ctr_origen());
    }

    public function test_set_and_get_ctr_origen()
    {
        $ctr_origenVo = new NombreCentroText('Test');
        $this->Traslado->setCtrOrigenVo($ctr_origenVo);
        $this->assertInstanceOf(NombreCentroText::class, $this->Traslado->getCtrOrigenVo());
        $this->assertEquals('Test', $this->Traslado->getCtrOrigenVo()->value());
    }

    public function test_set_and_get_id_ctr_destino()
    {
        $this->Traslado->setId_ctr_destino(1);
        $this->assertEquals(1, $this->Traslado->getId_ctr_destino());
    }

    public function test_set_and_get_ctr_destino()
    {
        $ctr_destinoVo = new NombreCentroText('Test');
        $this->Traslado->setCtrDestinoVo($ctr_destinoVo);
        $this->assertInstanceOf(NombreCentroText::class, $this->Traslado->getCtrDestinoVo());
        $this->assertEquals('Test', $this->Traslado->getCtrDestinoVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservText('Test');
        $this->Traslado->setObservVo($observVo);
        $this->assertInstanceOf(ObservText::class, $this->Traslado->getObservVo());
        $this->assertEquals('Test', $this->Traslado->getObservVo()->value());
    }

    public function test_set_all_attributes()
    {
        $traslado = new Traslado();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'f_traslado' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo_cmb' => new TrasladoTipoCmbCode('sede'),
            'id_ctr_origen' => 1,
            'ctr_origen' => new NombreCentroText('Test'),
            'id_ctr_destino' => 1,
            'ctr_destino' => new NombreCentroText('Test'),
            'observ' => new ObservText('Test'),
        ];
        $traslado->setAllAttributes($attributes);

        $this->assertEquals(1, $traslado->getId_item());
        $this->assertEquals(1, $traslado->getId_nom());
        $this->assertEquals('2024-01-15 10:30:00', $traslado->getF_traslado()->format('Y-m-d H:i:s'));
        $this->assertEquals('sede', $traslado->getTipoCmbVo()->value());
        $this->assertEquals(1, $traslado->getId_ctr_origen());
        $this->assertEquals('Test', $traslado->getCtrOrigenVo()->value());
        $this->assertEquals(1, $traslado->getId_ctr_destino());
        $this->assertEquals('Test', $traslado->getCtrDestinoVo()->value());
        $this->assertEquals('Test', $traslado->getObservVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $traslado = new Traslado();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'f_traslado' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo_cmb' => 'sede',
            'id_ctr_origen' => 1,
            'ctr_origen' => 'Test',
            'id_ctr_destino' => 1,
            'ctr_destino' => 'Test',
            'observ' => 'Test',
        ];
        $traslado->setAllAttributes($attributes);

        $this->assertEquals(1, $traslado->getId_item());
        $this->assertEquals(1, $traslado->getId_nom());
        $this->assertEquals('2024-01-15 10:30:00', $traslado->getF_traslado()->format('Y-m-d H:i:s'));
        $this->assertEquals('sede', $traslado->getTipoCmbVo()->value());
        $this->assertEquals(1, $traslado->getId_ctr_origen());
        $this->assertEquals('Test', $traslado->getCtrOrigenVo()->value());
        $this->assertEquals(1, $traslado->getId_ctr_destino());
        $this->assertEquals('Test', $traslado->getCtrDestinoVo()->value());
        $this->assertEquals('Test', $traslado->getObservVo()->value());
    }
}
