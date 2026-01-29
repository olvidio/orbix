<?php

namespace Tests\unit\personas\domain\entity;

use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\value_objects\CeCurso;
use src\personas\domain\value_objects\CeLugarText;
use src\personas\domain\value_objects\CeNumber;
use Tests\myTest;

class PersonaAgdTest extends myTest
{
    private PersonaAgd $PersonaAgd;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaAgd = new PersonaAgd();
    }

    public function test_set_and_get_ce()
    {
        $ceVo = new CeCurso(1001);
        $this->PersonaAgd->setCeVo($ceVo);
        $this->assertInstanceOf(CeCurso::class, $this->PersonaAgd->getCeVo());
        $this->assertEquals(1001, $this->PersonaAgd->getCeVo()->value());
    }

    public function test_set_and_get_ce_ini()
    {
        $ce_iniVo = new CeNumber(1001);
        $this->PersonaAgd->setCeIniVo($ce_iniVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaAgd->getCeIniVo());
        $this->assertEquals(1001, $this->PersonaAgd->getCeIniVo()->value());
    }

    public function test_set_and_get_ce_fin()
    {
        $ce_finVo = new CeNumber(1001);
        $this->PersonaAgd->setCeFinVo($ce_finVo);
        $this->assertInstanceOf(CeNumber::class, $this->PersonaAgd->getCeFinVo());
        $this->assertEquals(1001, $this->PersonaAgd->getCeFinVo()->value());
    }

    public function test_set_and_get_ce_lugar()
    {
        $ce_lugarVo = new CeLugarText('Test');
        $this->PersonaAgd->setCeLugarVo($ce_lugarVo);
        $this->assertInstanceOf(CeLugarText::class, $this->PersonaAgd->getCeLugarVo());
        $this->assertEquals('Test', $this->PersonaAgd->getCeLugarVo()->value());
    }

    public function test_set_all_attributes()
    {
        $personaAgd = new PersonaAgd();
        $attributes = [
            'ce' => new CeCurso(1001),
            'ce_ini' => new CeNumber(1001),
            'ce_fin' => new CeNumber(1001),
            'ce_lugar' => new CeLugarText('Test'),
        ];
        $personaAgd->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaAgd->getCeVo()->value());
        $this->assertEquals(1001, $personaAgd->getCeIniVo()->value());
        $this->assertEquals(1001, $personaAgd->getCeFinVo()->value());
        $this->assertEquals('Test', $personaAgd->getCeLugarVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaAgd = new PersonaAgd();
        $attributes = [
            'ce' => 1001,
            'ce_ini' => 1001,
            'ce_fin' => 1001,
            'ce_lugar' => 'Test',
        ];
        $personaAgd->setAllAttributes($attributes);

        $this->assertEquals(1001, $personaAgd->getCeVo()->value());
        $this->assertEquals(1001, $personaAgd->getCeIniVo()->value());
        $this->assertEquals(1001, $personaAgd->getCeFinVo()->value());
        $this->assertEquals('Test', $personaAgd->getCeLugarVo()->value());
    }
}
