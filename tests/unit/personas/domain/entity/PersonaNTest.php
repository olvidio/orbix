<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaN;
use src\personas\domain\value_objects\CeCurso;
use src\personas\domain\value_objects\CeLugarText;
use src\personas\domain\value_objects\CeNumber;
use Tests\myTest;

class PersonaNTest extends myTest
{
    private PersonaN $PersonaN;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaN = new PersonaN();
    }

    public function test_set_and_get_ce()
    {
        $ceVo = new CeCurso(1001);
        $this->PersonaN->setCeVo($ceVo);
        $this->assertInstanceOf(CeCurso::class, $this->PersonaN->getCeVo());
        $this->assertEquals(1001, $this->PersonaN->getCeVo()->value());
    }

    public function test_set_and_get_ce_ini()
    {
        $ce_iniVo = new CeNumber(1001);
        $this->PersonaN->setCeIniVo($ce_iniVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaN->getCeIniVo());
        $this->assertEquals(1001, $this->PersonaN->getCeIniVo()->value());
    }

    public function test_set_and_get_ce_fin()
    {
        $ce_finVo = new CeNumber(1001);
        $this->PersonaN->setCeFinVo($ce_finVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaN->getCeFinVo());
        $this->assertEquals(1001, $this->PersonaN->getCeFinVo()->value());
    }

    public function test_set_and_get_ce_lugar()
    {
        $ce_lugarVo = new CeLugarText('Test');
        $this->PersonaN->setCeLugarVo($ce_lugarVo);
        $this->assertInstanceOf(CeLugarText::class, $this->PersonaN->getCeLugarVo());
        $this->assertEquals('Test', $this->PersonaN->getCeLugarVo()->value());
    }

    public function test_set_all_attributes()
    {
        $personaN = new PersonaN();
        $attributes = [
            'ce' => new CeCurso(1001),
            'ce_ini' => new CeNumber(1001),
            'ce_fin' => new CeNumber(1001),
            'ce_lugar' => new CeLugarText('Test'),
        ];
        $personaN->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaN->getCeVo()->value());
        $this->assertEquals(1001, $personaN->getCeIniVo()->value());
        $this->assertEquals(1001, $personaN->getCeFinVo()->value());
        $this->assertEquals('Test', $personaN->getCeLugarVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaN = new PersonaN();
        $attributes = [
            'ce' => 1001,
            'ce_ini' => 1001,
            'ce_fin' => 1001,
            'ce_lugar' => 'Test',
        ];
        $personaN->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaN->getCeVo()->value());
        $this->assertEquals(1001, $personaN->getCeIniVo()->value());
        $this->assertEquals(1001, $personaN->getCeFinVo()->value());
        $this->assertEquals('Test', $personaN->getCeLugarVo()->value());
    }
}
