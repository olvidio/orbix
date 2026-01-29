<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaS;
use src\personas\domain\value_objects\CeCurso;
use src\personas\domain\value_objects\CeLugarText;
use src\personas\domain\value_objects\CeNumber;
use Tests\myTest;

class PersonaSTest extends myTest
{
    private PersonaS $PersonaS;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaS = new PersonaS();
    }

    public function test_set_and_get_ce()
    {
        $ceVo = new CeCurso(1001);
        $this->PersonaS->setCeVo($ceVo);
        $this->assertInstanceOf(CeCurso::class, $this->PersonaS->getCeVo());
        $this->assertEquals(1001, $this->PersonaS->getCeVo()->value());
    }

    public function test_set_and_get_ce_ini()
    {
        $ce_iniVo = new CeNumber(1001);
        $this->PersonaS->setCeIniVo($ce_iniVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaS->getCeIniVo());
        $this->assertEquals(1001, $this->PersonaS->getCeIniVo()->value());
    }

    public function test_set_and_get_ce_fin()
    {
        $ce_finVo = new CeNumber(1001);
        $this->PersonaS->setCeFinVo($ce_finVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaS->getCeFinVo());
        $this->assertEquals(1001, $this->PersonaS->getCeFinVo()->value());
    }

    public function test_set_and_get_ce_lugar()
    {
        $ce_lugarVo = new CeLugarText('Test');
        $this->PersonaS->setCeLugarVo($ce_lugarVo);
        $this->assertInstanceOf(CeLugarText::class, $this->PersonaS->getCeLugarVo());
        $this->assertEquals('Test', $this->PersonaS->getCeLugarVo()->value());
    }

    public function test_set_all_attributes()
    {
        $personaS = new PersonaS();
        $attributes = [
            'ce' => new CeCurso(1001),
            'ce_ini' => new CeNumber(1001),
            'ce_fin' => new CeNumber(1001),
            'ce_lugar' => new CeLugarText('Test'),
        ];
        $personaS->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaS->getCeVo()->value());
        $this->assertEquals(1001, $personaS->getCeIniVo()->value());
        $this->assertEquals(1001, $personaS->getCeFinVo()->value());
        $this->assertEquals('Test', $personaS->getCeLugarVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaS = new PersonaS();
        $attributes = [
            'ce' => 1001,
            'ce_ini' => 1001,
            'ce_fin' => 1001,
            'ce_lugar' => 'Test',
        ];
        $personaS->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaS->getCeVo()->value());
        $this->assertEquals(1001, $personaS->getCeIniVo()->value());
        $this->assertEquals(1001, $personaS->getCeFinVo()->value());
        $this->assertEquals('Test', $personaS->getCeLugarVo()->value());
    }
}
