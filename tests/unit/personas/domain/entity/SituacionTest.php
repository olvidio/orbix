<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\Situacion;
use src\personas\domain\value_objects\SituacionCode;
use src\personas\domain\value_objects\SituacionName;
use Tests\myTest;

class SituacionTest extends myTest
{
    private Situacion $Situacion;

    public function setUp(): void
    {
        parent::setUp();
        $this->Situacion = new Situacion();
        $this->Situacion->setSituacionVo(new SituacionCode('A'));
    }

    public function test_set_and_get_situacion()
    {
        $situacionVo = new SituacionCode('A');
        $this->Situacion->setSituacionVo($situacionVo);
        $this->assertInstanceOf(SituacionCode::class, $this->Situacion->getSituacionVo());
        $this->assertEquals('A', $this->Situacion->getSituacionVo()->value());
    }

    public function test_set_and_get_nombreSituacion()
    {
        $nombreSituacionVo = new SituacionName('Test Name');
        $this->Situacion->setNombreSituacionVo($nombreSituacionVo);
        $this->assertInstanceOf(SituacionName::class, $this->Situacion->getNombreSituacionVo());
        $this->assertEquals('Test Name', $this->Situacion->getNombreSituacionVo()->value());
    }

    public function test_set_all_attributes()
    {
        $situacion = new Situacion();
        $attributes = [
            'situacion' => new SituacionCode('A'),
            'nombreSituacion' => new SituacionName('Test Name'),
        ];
        $situacion->setAllAttributes($attributes);

        $this->assertEquals('A', $situacion->getSituacionVo()->value());
        $this->assertEquals('Test Name', $situacion->getNombreSituacionVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $situacion = new Situacion();
        $attributes = [
            'situacion' => 'A',
            'nombreSituacion' => 'Test Name',
        ];
        $situacion->setAllAttributes($attributes);

        $this->assertEquals('A', $situacion->getSituacionVo()->value());
        $this->assertEquals('Test Name', $situacion->getNombreSituacionVo()->value());
    }
}
