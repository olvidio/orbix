<?php

namespace Tests\unit\configuracion\domain\entity;

use ReflectionProperty;
use src\configuracion\domain\entity\Config;
use Tests\myTest;

class ConfigTest extends myTest
{
    private Config $Config;

    public function setUp(): void
    {
        parent::setUp();
        $this->Config = new Config();
    }

    private function setCursoStgr(array $value): void
    {
        $prop = new ReflectionProperty(Config::class, 'aCursoStgr');
        $prop->setValue($this->Config, $value);
    }

    private function setCursoCrt(array $value): void
    {
        $prop = new ReflectionProperty(Config::class, 'aCursoCrt');
        $prop->setValue($this->Config, $value);
    }

    public function test_getCursoStgr()
    {
        $aCursoStgr = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6];
        $this->setCursoStgr($aCursoStgr);

        $this->assertEquals($aCursoStgr, $this->Config->getCursoStgr());
    }

    public function test_getCursoCrt()
    {
        $aCursoCrt = ['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5];
        $this->setCursoCrt($aCursoCrt);

        $this->assertEquals($aCursoCrt, $this->Config->getCursoCrt());
    }

    public function test_getDiaIniStgr()
    {
        $this->setCursoStgr(['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6]);

        $this->assertEquals(1, $this->Config->getDiaIniStgr());
    }

    public function test_getMesIniStgr()
    {
        $this->setCursoStgr(['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6]);

        $this->assertEquals(9, $this->Config->getMesIniStgr());
    }

    public function test_getDiaFinStgr()
    {
        $this->setCursoStgr(['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6]);

        $this->assertEquals(30, $this->Config->getDiaFinStgr());
    }

    public function test_getMesFinStgr()
    {
        $this->setCursoStgr(['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6]);

        $this->assertEquals(6, $this->Config->getMesFinStgr());
    }

    public function test_getDiaIniCrt()
    {
        $this->setCursoCrt(['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5]);

        $this->assertEquals(15, $this->Config->getDiaIniCrt());
    }

    public function test_getMesIniCrt()
    {
        $this->setCursoCrt(['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5]);

        $this->assertEquals(10, $this->Config->getMesIniCrt());
    }

    public function test_getDiaFinCrt()
    {
        $this->setCursoCrt(['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5]);

        $this->assertEquals(20, $this->Config->getDiaFinCrt());
    }

    public function test_getMesFinCrt()
    {
        $this->setCursoCrt(['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5]);

        $this->assertEquals(5, $this->Config->getMesFinCrt());
    }
}
