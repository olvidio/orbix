<?php

namespace Tests\unit\configuracion\domain\entity;

use src\configuracion\domain\entity\Config;
use Tests\myTest;

class ConfigTest extends myTest
{
    private Config $Config;

    public function setUp(): void
    {
        parent::setUp();
        $this->Config = new Config();
        $this->Config->setCursoStgr([]);
        $this->Config->setCursoCrt([]);
    }

    public function test_set_and_get_aCursoStgr()
    {
        $this->Config->setCursoStgr([]);
        $this->assertEquals([], $this->Config->getCursoStgr());
    }

    public function test_set_and_get_aCursoCrt()
    {
        $this->Config->setCursoCrt([]);
        $this->assertEquals([], $this->Config->getCursoCrt());
    }


    public function test_set_all_attributes()
    {
        $config = new Config();
        $attributes = [
            'aCursoStgr' => [],
            'aCursoCrt' => [],
        ];
        $config->setAllAttributes($attributes);

        $this->assertEquals([], $config->getCursoStgr());
        $this->assertEquals([], $config->getCursoCrt());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $config = new Config();
        $attributes = [
            'aCursoStgr' => [],
            'aCursoCrt' => [],
        ];
        $config->setAllAttributes($attributes);

        $this->assertEquals([], $config->getCursoStgr());
        $this->assertEquals([], $config->getCursoCrt());
    }
}
