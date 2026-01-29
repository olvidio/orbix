<?php

namespace Tests\unit\cambios\domain\entity;

use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\entity\Cambio;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class CambioTest extends myTest
{
    private Cambio $Cambio;

    public function setUp(): void
    {
        parent::setUp();
        $this->Cambio = new Cambio();
        $this->Cambio->setId_schema(1);
        $this->Cambio->setId_item_cambio(1);
    }

    public function test_set_and_get_id_schema()
    {
        $this->Cambio->setId_schema(1);
        $this->assertEquals(1, $this->Cambio->getId_schema());
    }

    public function test_set_and_get_id_item_cambio()
    {
        $this->Cambio->setId_item_cambio(1);
        $this->assertEquals(1, $this->Cambio->getId_item_cambio());
    }

    public function test_set_and_get_id_tipo_cambio()
    {
        $this->Cambio->setId_tipo_cambio(1);
        $this->assertEquals(1, $this->Cambio->getId_tipo_cambio());
    }

    public function test_set_and_get_id_activ()
    {
        $this->Cambio->setId_activ(1);
        $this->assertEquals(1, $this->Cambio->getId_activ());
    }

    public function test_set_and_get_id_tipo_activ()
    {
        $id_tipo_activVo = new ActividadTipoId(123456);
        $this->Cambio->setIdTipoActivVo($id_tipo_activVo);
        $this->assertInstanceOf(ActividadTipoId::class, $this->Cambio->getIdTipoActivVo());
        $this->assertEquals(123456, $this->Cambio->getIdTipoActivVo()->value());
    }

    public function test_set_and_get_id_status()
    {
        $id_statusVo = new StatusId(1);
        $this->Cambio->setIdStatusVo($id_statusVo);
        $this->assertInstanceOf(StatusId::class, $this->Cambio->getIdStatusVo());
        $this->assertEquals(1, $this->Cambio->getIdStatusVo()->value());
    }

    public function test_set_and_get_valor_old()
    {
        $this->Cambio->setValor_old('test');
        $this->assertEquals('test', $this->Cambio->getValor_old());
    }

    public function test_set_and_get_valor_new()
    {
        $this->Cambio->setValor_new('test');
        $this->assertEquals('test', $this->Cambio->getValor_new());
    }

    public function test_set_and_get_quien_cambia()
    {
        $this->Cambio->setQuien_cambia(1);
        $this->assertEquals(1, $this->Cambio->getQuien_cambia());
    }

    public function test_set_and_get_sfsv_quien_cambia()
    {
        $this->Cambio->setSfsv_quien_cambia(1);
        $this->assertEquals(1, $this->Cambio->getSfsv_quien_cambia());
    }

    public function test_set_and_get_timestamp_cambio()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Cambio->setTimestamp_cambio($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Cambio->getTimestamp_cambio());
        $this->assertEquals('2024-01-15 10:30:00', $this->Cambio->getTimestamp_cambio()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $cambio = new Cambio();
        $attributes = [
            'id_schema' => 1,
            'id_item_cambio' => 1,
            'id_tipo_cambio' => 1,
            'id_activ' => 1,
            'id_tipo_activ' => new ActividadTipoId(123456),
            'id_status' => new StatusId(1),
            'valor_old' => 'test',
            'valor_new' => 'test',
            'quien_cambia' => 1,
            'sfsv_quien_cambia' => 1,
            'timestamp_cambio' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $cambio->setAllAttributes($attributes);

        $this->assertEquals(1, $cambio->getId_schema());
        $this->assertEquals(1, $cambio->getId_item_cambio());
        $this->assertEquals(1, $cambio->getId_tipo_cambio());
        $this->assertEquals(1, $cambio->getId_activ());
        $this->assertEquals(123456, $cambio->getIdTipoActivVo()->value());
        $this->assertEquals(1, $cambio->getIdStatusVo()->value());
        $this->assertEquals('test', $cambio->getValor_old());
        $this->assertEquals('test', $cambio->getValor_new());
        $this->assertEquals(1, $cambio->getQuien_cambia());
        $this->assertEquals(1, $cambio->getSfsv_quien_cambia());
        $this->assertEquals('2024-01-15 10:30:00', $cambio->getTimestamp_cambio()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $cambio = new Cambio();
        $attributes = [
            'id_schema' => 1,
            'id_item_cambio' => 1,
            'id_tipo_cambio' => 1,
            'id_activ' => 1,
            'id_tipo_activ' => 123456,
            'id_status' => 1,
            'valor_old' => 'test',
            'valor_new' => 'test',
            'quien_cambia' => 1,
            'sfsv_quien_cambia' => 1,
            'timestamp_cambio' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $cambio->setAllAttributes($attributes);

        $this->assertEquals(1, $cambio->getId_schema());
        $this->assertEquals(1, $cambio->getId_item_cambio());
        $this->assertEquals(1, $cambio->getId_tipo_cambio());
        $this->assertEquals(1, $cambio->getId_activ());
        $this->assertEquals(123456, $cambio->getIdTipoActivVo()->value());
        $this->assertEquals(1, $cambio->getIdStatusVo()->value());
        $this->assertEquals('test', $cambio->getValor_old());
        $this->assertEquals('test', $cambio->getValor_new());
        $this->assertEquals(1, $cambio->getQuien_cambia());
        $this->assertEquals(1, $cambio->getSfsv_quien_cambia());
        $this->assertEquals('2024-01-15 10:30:00', $cambio->getTimestamp_cambio()->format('Y-m-d H:i:s'));
    }
}
