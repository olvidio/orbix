<?php

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\TipoTeleco;
use src\ubis\domain\value_objects\int;
use src\ubis\domain\value_objects\TipoTelecoName;
use Tests\myTest;

class TipoTelecoTest extends myTest
{
    private TipoTeleco $TipoTeleco;

    public function setUp(): void
    {
        parent::setUp();
        $this->TipoTeleco = new TipoTeleco();
        $this->TipoTeleco->setId(1);
    }

    public function test_set_and_get_tipo_teleco()
    {
        $tipo_telecoVo = new int('TST');
        $this->TipoTeleco->setTipoTelecoVo($tipo_telecoVo);
        $this->assertInstanceOf(int::class, $this->TipoTeleco->getTipoTelecoVo());
        $this->assertEquals('TST', $this->TipoTeleco->getTipoTelecoVo()->value());
    }

    public function test_set_and_get_nombre_teleco()
    {
        $nombre_telecoVo = new TipoTelecoName('test');
        $this->TipoTeleco->setNombreTelecoVo($nombre_telecoVo);
        $this->assertInstanceOf(TipoTelecoName::class, $this->TipoTeleco->getNombreTelecoVo());
        $this->assertEquals('test', $this->TipoTeleco->getNombreTelecoVo()->value());
    }

    public function test_set_and_get_id()
    {
        $this->TipoTeleco->setId(1);
        $this->assertEquals(1, $this->TipoTeleco->getId());
    }

    public function test_set_all_attributes()
    {
        $tipoTeleco = new TipoTeleco();
        $attributes = [
            'tipo_teleco' => new int('TST'),
            'nombre_teleco' => new TipoTelecoName('test'),
            'id' => 1,
        ];
        $tipoTeleco->setAllAttributes($attributes);

        $this->assertEquals('TST', $tipoTeleco->getTipoTelecoVo()->value());
        $this->assertEquals('test', $tipoTeleco->getNombreTelecoVo()->value());
        $this->assertEquals(1, $tipoTeleco->getId());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tipoTeleco = new TipoTeleco();
        $attributes = [
            'tipo_teleco' => 'TST',
            'nombre_teleco' => 'test',
            'id' => 1,
        ];
        $tipoTeleco->setAllAttributes($attributes);

        $this->assertEquals('TST', $tipoTeleco->getTipoTelecoVo()->value());
        $this->assertEquals('test', $tipoTeleco->getNombreTelecoVo()->value());
        $this->assertEquals(1, $tipoTeleco->getId());
    }
}
