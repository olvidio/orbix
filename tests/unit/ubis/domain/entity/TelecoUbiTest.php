<?php

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\TelecoUbi;
use src\ubis\domain\value_objects\int;
use src\ubis\domain\value_objects\NumTelecoText;
use src\ubis\domain\value_objects\ObservTelecoText;
use src\ubis\domain\value_objects\TelecoUbiId;
use src\ubis\domain\value_objects\TipoTelecoId;
use Tests\myTest;

class TelecoUbiTest extends myTest
{
    private TelecoUbi $TelecoUbi;

    public function setUp(): void
    {
        parent::setUp();
        $this->TelecoUbi = new TelecoUbi();
        $this->TelecoUbi->setIdUbiVo(new TelecoUbiId(1));
        $this->TelecoUbi->setIdTipoTelecoVo(new TipoTelecoId(1));
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new TelecoUbiId(1);
        $this->TelecoUbi->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(TelecoUbiId::class, $this->TelecoUbi->getIdUbiVo());
        $this->assertEquals(1, $this->TelecoUbi->getIdUbiVo()->value());
    }

    public function test_set_and_get_id_tipo_teleco()
    {
        $id_tipo_telecoVo = new TipoTelecoId(1);
        $this->TelecoUbi->setIdTipoTelecoVo($id_tipo_telecoVo);
        $this->assertInstanceOf(TipoTelecoId::class, $this->TelecoUbi->getIdTipoTelecoVo());
        $this->assertEquals(1, $this->TelecoUbi->getIdTipoTelecoVo()->value());
    }

    public function test_set_and_get_desc_teleco()
    {
        $desc_telecoVo = new int('Test');
        $this->TelecoUbi->setId_desc_teleco($desc_telecoVo);
        $this->assertInstanceOf(int::class, $this->TelecoUbi->getId_desc_teleco());
        $this->assertEquals('Test', $this->TelecoUbi->getId_desc_teleco()->value());
    }

    public function test_set_and_get_num_teleco()
    {
        $num_telecoVo = new NumTelecoText('Test');
        $this->TelecoUbi->setNumTelecoVo($num_telecoVo);
        $this->assertInstanceOf(NumTelecoText::class, $this->TelecoUbi->getNumTelecoVo());
        $this->assertEquals('Test', $this->TelecoUbi->getNumTelecoVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservTelecoText('Test');
        $this->TelecoUbi->setObservVo($observVo);
        $this->assertInstanceOf(ObservTelecoText::class, $this->TelecoUbi->getObservVo());
        $this->assertEquals('Test', $this->TelecoUbi->getObservVo()->value());
    }

    public function test_set_and_get_id_item()
    {
        $this->TelecoUbi->setId_item(1);
        $this->assertEquals(1, $this->TelecoUbi->getId_item());
    }

    public function test_set_all_attributes()
    {
        $telecoUbi = new TelecoUbi();
        $attributes = [
            'id_ubi' => new TelecoUbiId(1),
            'id_tipo_teleco' => new TipoTelecoId(1),
            'id_desc_teleco' => 3,
            'num_teleco' => new NumTelecoText('Test'),
            'observ' => new ObservTelecoText('Test'),
            'id_item' => 1,
        ];
        $telecoUbi->setAllAttributes($attributes);

        $this->assertEquals(1, $telecoUbi->getIdUbiVo()->value());
        $this->assertEquals(1, $telecoUbi->getId_tipo_teleco());
        $this->assertEquals(3, $telecoUbi->getId_desc_teleco());
        $this->assertEquals('Test', $telecoUbi->getNumTelecoVo()->value());
        $this->assertEquals('Test', $telecoUbi->getObservVo()->value());
        $this->assertEquals(1, $telecoUbi->getId_item());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $telecoUbi = new TelecoUbi();
        $attributes = [
            'id_ubi' => 1,
            'id_tipo_teleco' => 1,
            'id_desc_teleco' => 3,
            'num_teleco' => 'Test',
            'observ' => 'Test',
            'id_item' => 1,
        ];
        $telecoUbi->setAllAttributes($attributes);

        $this->assertEquals(1, $telecoUbi->getIdUbiVo()->value());
        $this->assertEquals(1, $telecoUbi->getIdTipoTelecoVo()->value());
        $this->assertEquals(3, $telecoUbi->getId_desc_teleco()->value());
        $this->assertEquals('Test', $telecoUbi->getNumTelecoVo()->value());
        $this->assertEquals('Test', $telecoUbi->getObservVo()->value());
        $this->assertEquals(1, $telecoUbi->getId_item());
    }
}
