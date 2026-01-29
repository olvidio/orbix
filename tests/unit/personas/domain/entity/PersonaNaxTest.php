<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaNax;
use src\personas\domain\value_objects\CeCurso;
use src\personas\domain\value_objects\CeLugarText;
use src\personas\domain\value_objects\CeNumber;
use Tests\myTest;

class PersonaNaxTest extends myTest
{
    private PersonaNax $PersonaNax;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaNax = new PersonaNax();
    }

    public function test_set_and_get_ce()
    {
        $ceVo = new CeCurso(1001);
        $this->PersonaNax->setCeVo($ceVo);
        $this->assertInstanceOf(CeCurso::class, $this->PersonaNax->getCeVo());
        $this->assertEquals(1001, $this->PersonaNax->getCeVo()->value());
    }

    public function test_set_and_get_ce_ini()
    {
        $ce_iniVo = new CeNumber(1001);
        $this->PersonaNax->setCeIniVo($ce_iniVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaNax->getCeIniVo());
        $this->assertEquals(1001, $this->PersonaNax->getCeIniVo()->value());
    }

    public function test_set_and_get_ce_fin()
    {
        $ce_finVo = new CeNumber(1001);
        $this->PersonaNax->setCeFinVo($ce_finVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaNax->getCeFinVo());
        $this->assertEquals(1001, $this->PersonaNax->getCeFinVo()->value());
    }

    public function test_set_and_get_ce_lugar()
    {
        $ce_lugarVo = new CeLugarText('Test');
        $this->PersonaNax->setCeLugarVo($ce_lugarVo);
        $this->assertInstanceOf(CeLugarText::class, $this->PersonaNax->getCeLugarVo());
        $this->assertEquals('Test', $this->PersonaNax->getCeLugarVo()->value());
    }

    public function test_set_all_attributes()
    {
        $personaNax = new PersonaNax();
        $attributes = [
            'ce' => new CeCurso(1001),
            'ce_ini' => new CeNumber(1001),
            'ce_fin' => new CeNumber(1001),
            'ce_lugar' => new CeLugarText('Test'),
        ];
        $personaNax->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaNax->getCeVo()->value());
        $this->assertEquals(1001, $personaNax->getCeIniVo()->value());
        $this->assertEquals(1001, $personaNax->getCeFinVo()->value());
        $this->assertEquals('Test', $personaNax->getCeLugarVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaNax = new PersonaNax();
        $attributes = [
            'ce' => 1001,
            'ce_ini' => 1001,
            'ce_fin' => 1001,
            'ce_lugar' => 'Test',
        ];
        $personaNax->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaNax->getCeVo()->value());
        $this->assertEquals(1001, $personaNax->getCeIniVo()->value());
        $this->assertEquals(1001, $personaNax->getCeFinVo()->value());
        $this->assertEquals('Test', $personaNax->getCeLugarVo()->value());
    }
}
