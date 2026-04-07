<?php

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\TipoCentro;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoCentroName;
use Tests\myTest;

class TipoCentroTest extends myTest
{
    private TipoCentro $TipoCentro;

    public function setUp(): void
    {
        parent::setUp();
        $this->TipoCentro = new TipoCentro();
        $this->TipoCentro->setTipoCtrVo(new TipoCentroCode('ctrdl'));
    }

    public function test_set_and_get_tipo_centro()
    {
        $tipo_centroVo = new TipoCentroCode('ctrdl');
        $this->TipoCentro->setTipoCtrVo($tipo_centroVo);
        $this->assertInstanceOf(TipoCentroCode::class, $this->TipoCentro->getTipoCtrVo());
        $this->assertEquals('ctrdl', $this->TipoCentro->getTipoCtrVo()->value());
    }

    public function test_set_and_get_nombre_tipo_centro()
    {
        $nombre_tipo_centroVo = new TipoCentroName('test');
        $this->TipoCentro->setNombreTipoCtrVo($nombre_tipo_centroVo);
        $this->assertInstanceOf(TipoCentroName::class, $this->TipoCentro->getNombreTipoCtrVo());
        $this->assertEquals('test', $this->TipoCentro->getNombreTipoCtrVo()->value());
    }

    public function test_set_all_attributes()
    {
        $tipoCentro = new TipoCentro();
        $attributes = [
            'tipo_ctr' => new TipoCentroCode('ctrdl'),
            'nombre_tipo_ctr' => new TipoCentroName('test'),
        ];
        $tipoCentro->setAllAttributes($attributes);

        $this->assertEquals('ctrdl', $tipoCentro->getTipoCtrVo()->value());
        $this->assertEquals('test', $tipoCentro->getNombreTipoCtrVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tipoCentro = new TipoCentro();
        $attributes = [
            'tipo_ctr' => 'ctrdl',
            'nombre_tipo_ctr' => 'test',
        ];
        $tipoCentro->setAllAttributes($attributes);

        $this->assertEquals('ctrdl', $tipoCentro->getTipoCtrVo()->value());
        $this->assertEquals('test', $tipoCentro->getNombreTipoCtrVo()->value());
    }
}
