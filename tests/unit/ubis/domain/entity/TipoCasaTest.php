<?php

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\TipoCasa;
use src\ubis\domain\value_objects\TipoCasaCode;
use src\ubis\domain\value_objects\TipoCasaName;
use Tests\myTest;

class TipoCasaTest extends myTest
{
    private TipoCasa $TipoCasa;

    public function setUp(): void
    {
        parent::setUp();
        $this->TipoCasa = new TipoCasa();
        $this->TipoCasa->setTipoCasaVo(new TipoCasaCode('TST'));
    }

    public function test_set_and_get_tipo_casa()
    {
        $tipo_casaVo = new TipoCasaCode('TST');
        $this->TipoCasa->setTipoCasaVo($tipo_casaVo);
        $this->assertInstanceOf(TipoCasaCode::class, $this->TipoCasa->getTipoCasaVo());
        $this->assertEquals('TST', $this->TipoCasa->getTipoCasaVo()->value());
    }

    public function test_set_and_get_nombre_tipo_casa()
    {
        $nombre_tipo_casaVo = new TipoCasaName('test');
        $this->TipoCasa->setNombreTipoCasaVo($nombre_tipo_casaVo);
        $this->assertInstanceOf(TipoCasaName::class, $this->TipoCasa->getNombreTipoCasaVo());
        $this->assertEquals('test', $this->TipoCasa->getNombreTipoCasaVo()->value());
    }

    public function test_set_all_attributes()
    {
        $tipoCasa = new TipoCasa();
        $attributes = [
            'tipo_casa' => new TipoCasaCode('TST'),
            'nombre_tipo_casa' => new TipoCasaName('test'),
        ];
        $tipoCasa->setAllAttributes($attributes);

        $this->assertEquals('TST', $tipoCasa->getTipoCasaVo()->value());
        $this->assertEquals('test', $tipoCasa->getNombreTipoCasaVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tipoCasa = new TipoCasa();
        $attributes = [
            'tipo_casa' => 'TST',
            'nombre_tipo_casa' => 'test',
        ];
        $tipoCasa->setAllAttributes($attributes);

        $this->assertEquals('TST', $tipoCasa->getTipoCasaVo()->value());
        $this->assertEquals('test', $tipoCasa->getNombreTipoCasaVo()->value());
    }
}
