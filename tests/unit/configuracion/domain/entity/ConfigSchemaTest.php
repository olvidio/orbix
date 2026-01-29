<?php

namespace Tests\unit\configuracion\domain\entity;

use src\configuracion\domain\entity\ConfigSchema;
use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;
use Tests\myTest;

class ConfigSchemaTest extends myTest
{
    private ConfigSchema $ConfigSchema;

    public function setUp(): void
    {
        parent::setUp();
        $this->ConfigSchema = new ConfigSchema();
        $this->ConfigSchema->setParametroVo(new ConfigParametroCode('TST'));
    }

    public function test_set_and_get_parametro()
    {
        $parametroVo = new ConfigParametroCode('TST');
        $this->ConfigSchema->setParametroVo($parametroVo);
        $this->assertInstanceOf(ConfigParametroCode::class, $this->ConfigSchema->getParametroVo());
        $this->assertEquals('TST', $this->ConfigSchema->getParametroVo()->value());
    }

    public function test_set_and_get_valor()
    {
        $valorVo = new ConfigValor('test');
        $this->ConfigSchema->setValorVo($valorVo);
        $this->assertInstanceOf(ConfigValor::class, $this->ConfigSchema->getValorVo());
        $this->assertEquals('test', $this->ConfigSchema->getValorVo()->value());
    }

    public function test_set_all_attributes()
    {
        $configSchema = new ConfigSchema();
        $attributes = [
            'parametro' => new ConfigParametroCode('TST'),
            'valor' => new ConfigValor('test'),
        ];
        $configSchema->setAllAttributes($attributes);

        $this->assertEquals('TST', $configSchema->getParametroVo()->value());
        $this->assertEquals('test', $configSchema->getValorVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $configSchema = new ConfigSchema();
        $attributes = [
            'parametro' => 'TST',
            'valor' => 'test',
        ];
        $configSchema->setAllAttributes($attributes);

        $this->assertEquals('TST', $configSchema->getParametroVo()->value());
        $this->assertEquals('test', $configSchema->getValorVo()->value());
    }
}
