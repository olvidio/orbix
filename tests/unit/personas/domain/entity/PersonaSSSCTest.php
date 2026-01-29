<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaSSSC;
use src\personas\domain\value_objects\CeCurso;
use src\personas\domain\value_objects\CeLugarText;
use src\personas\domain\value_objects\CeNumber;
use Tests\myTest;

class PersonaSSSCTest extends myTest
{
    private PersonaSSSC $PersonaSSSC;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaSSSC = new PersonaSSSC();
    }

    public function test_set_and_get_ce()
    {
        $ceVo = new CeCurso(1001);
        $this->PersonaSSSC->setCeVo($ceVo);
        $this->assertInstanceOf(CeCurso::class, $this->PersonaSSSC->getCeVo());
        $this->assertEquals(1001, $this->PersonaSSSC->getCeVo()->value());
    }

    public function test_set_and_get_ce_ini()
    {
        $ce_iniVo = new CeNumber(1001);
        $this->PersonaSSSC->setCeIniVo($ce_iniVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaSSSC->getCeIniVo());
        $this->assertEquals(1001, $this->PersonaSSSC->getCeIniVo()->value());
    }

    public function test_set_and_get_ce_fin()
    {
        $ce_finVo = new CeNumber(1001);
        $this->PersonaSSSC->setCeFinVo($ce_finVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaSSSC->getCeFinVo());
        $this->assertEquals(1001, $this->PersonaSSSC->getCeFinVo()->value());
    }

    public function test_set_and_get_ce_lugar()
    {
        $ce_lugarVo = new CeLugarText('Test');
        $this->PersonaSSSC->setCeLugarVo($ce_lugarVo);
        $this->assertInstanceOf(CeLugarText::class, $this->PersonaSSSC->getCeLugarVo());
        $this->assertEquals('Test', $this->PersonaSSSC->getCeLugarVo()->value());
    }

    public function test_set_all_attributes()
    {
        $personaSSSC = new PersonaSSSC();
        $attributes = [
            'ce' => new CeCurso(1001),
            'ce_ini' => new CeNumber(1001),
            'ce_fin' => new CeNumber(1001),
            'ce_lugar' => new CeLugarText('Test'),
        ];
        $personaSSSC->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaSSSC->getCeVo()->value());
        $this->assertEquals(1001, $personaSSSC->getCeIniVo()->value());
        $this->assertEquals(1001, $personaSSSC->getCeFinVo()->value());
        $this->assertEquals('Test', $personaSSSC->getCeLugarVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaSSSC = new PersonaSSSC();
        $attributes = [
            'ce' => 1001,
            'ce_ini' => 1001,
            'ce_fin' => 1001,
            'ce_lugar' => 'Test',
        ];
        $personaSSSC->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaSSSC->getCeVo()->value());
        $this->assertEquals(1001, $personaSSSC->getCeIniVo()->value());
        $this->assertEquals(1001, $personaSSSC->getCeFinVo()->value());
        $this->assertEquals('Test', $personaSSSC->getCeLugarVo()->value());
    }
}
