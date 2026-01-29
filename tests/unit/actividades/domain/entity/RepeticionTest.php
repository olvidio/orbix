<?php

namespace Tests\unit\actividades\domain\entity;

use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\value_objects\RepeticionId;
use src\actividades\domain\value_objects\RepeticionText;
use src\actividades\domain\value_objects\RepeticionTipo;
use src\actividades\domain\value_objects\TemporadaCode;
use Tests\myTest;

class RepeticionTest extends myTest
{
    private Repeticion $Repeticion;

    public function setUp(): void
    {
        parent::setUp();
        $this->Repeticion = new Repeticion();
        $this->Repeticion->setId_(1);
        $this->Repeticion->setRepeticionVo(new RepeticionText('Test value'));
    }

    public function test_get_id_repeticion()
    {
        $this->assertEquals(1, $this->Repeticion->getId_());
    }

    public function test_set_and_get_repeticion()
    {
        $repeticionVo = new RepeticionText('Test value');
        $this->Repeticion->setRepeticionVo($repeticionVo);
        $this->assertInstanceOf(RepeticionText::class, $this->Repeticion->getRepeticionVo());
        $this->assertEquals('Test value', $this->Repeticion->getRepeticionVo()->value());
    }

    public function test_set_and_get_temporada()
    {
        $temporadaVo = new TemporadaCode('T');
        $this->Repeticion->setTemporadaVo($temporadaVo);
        $this->assertInstanceOf(TemporadaCode::class, $this->Repeticion->getTemporadaVo());
        $this->assertEquals('T', $this->Repeticion->getTemporadaVo()->value());
    }

    public function test_set_all_attributes()
    {
        $repeticion = new Repeticion();
        $attributes = [
            'id_repeticion' => 1,
            'repeticion' => new RepeticionText('Test value'),
            'temporada' => new TemporadaCode('T'),
        ];
        $repeticion->setAllAttributes($attributes);

        $this->assertEquals(1, $repeticion->getId_());
        $this->assertEquals('Test value', $repeticion->getRepeticionVo()->value());
        $this->assertEquals('T', $repeticion->getTemporadaVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $repeticion = new Repeticion();
        $attributes = [
            'id_repeticion' => 1,
            'repeticion' => 'Test value',
            'temporada' => 'T',
        ];
        $repeticion->setAllAttributes($attributes);

        $this->assertEquals(1, $repeticion->getId_());
        $this->assertEquals('Test value', $repeticion->getRepeticionVo()->value());
        $this->assertEquals('T', $repeticion->getTemporadaVo()->value());
    }
}
