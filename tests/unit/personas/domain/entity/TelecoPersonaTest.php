<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\TelecoPersona;
use src\ubis\domain\value_objects\NumTelecoText;
use src\ubis\domain\value_objects\ObservTelecoText;
use Tests\myTest;

class TelecoPersonaTest extends myTest
{
    private TelecoPersona $TelecoPersona;

    public function setUp(): void
    {
        parent::setUp();
        $this->TelecoPersona = new TelecoPersona();
        $this->TelecoPersona->setId_nom(1);
        $this->TelecoPersona->setId_item(1);
    }

    public function test_set_and_get_id_nom()
    {
        $this->TelecoPersona->setId_nom(1);
        $this->assertEquals(1, $this->TelecoPersona->getId_nom());
    }

    public function test_set_and_get_id_item()
    {
        $this->TelecoPersona->setId_item(1);
        $this->assertEquals(1, $this->TelecoPersona->getId_item());
    }

    public function test_set_and_get_id_tipo_teleco()
    {
        $this->TelecoPersona->setId_tipo_teleco(1);
        $this->assertEquals(1, $this->TelecoPersona->getId_tipo_teleco());
    }

    public function test_set_and_get_num_teleco()
    {
        $num_telecoVo = new NumTelecoText('Test');
        $this->TelecoPersona->setNumTelecoVo($num_telecoVo);
        $this->assertInstanceOf(NumTelecoText::class, $this->TelecoPersona->getNumTelecoVo());
        $this->assertEquals('Test', $this->TelecoPersona->getNumTelecoVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservTelecoText('Test');
        $this->TelecoPersona->setObservVo($observVo);
        $this->assertInstanceOf(ObservTelecoText::class, $this->TelecoPersona->getObservVo());
        $this->assertEquals('Test', $this->TelecoPersona->getObservVo()->value());
    }

    public function test_set_and_get_id_desc_teleco()
    {
        $this->TelecoPersona->setId_desc_teleco(1);
        $this->assertEquals(1, $this->TelecoPersona->getId_desc_teleco());
    }

    public function test_set_all_attributes()
    {
        $telecoPersona = new TelecoPersona();
        $attributes = [
            'id_nom' => 1,
            'id_item' => 1,
            'id_tipo_teleco' => 1,
            'num_teleco' => new NumTelecoText('Test'),
            'observ' => new ObservTelecoText('Test'),
            'id_desc_teleco' => 1,
        ];
        $telecoPersona->setAllAttributes($attributes);

        $this->assertEquals(1, $telecoPersona->getId_nom());
        $this->assertEquals(1, $telecoPersona->getId_item());
        $this->assertEquals(1, $telecoPersona->getId_tipo_teleco());
        $this->assertEquals('Test', $telecoPersona->getNumTelecoVo()->value());
        $this->assertEquals('Test', $telecoPersona->getObservVo()->value());
        $this->assertEquals(1, $telecoPersona->getId_desc_teleco());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $telecoPersona = new TelecoPersona();
        $attributes = [
            'id_nom' => 1,
            'id_item' => 1,
            'id_tipo_teleco' => 1,
            'num_teleco' => 'Test',
            'observ' => 'Test',
            'id_desc_teleco' => 1,
        ];
        $telecoPersona->setAllAttributes($attributes);

        $this->assertEquals(1, $telecoPersona->getId_nom());
        $this->assertEquals(1, $telecoPersona->getId_item());
        $this->assertEquals(1, $telecoPersona->getId_tipo_teleco());
        $this->assertEquals('Test', $telecoPersona->getNumTelecoVo()->value());
        $this->assertEquals('Test', $telecoPersona->getObservVo()->value());
        $this->assertEquals(1, $telecoPersona->getId_desc_teleco());
    }
}
