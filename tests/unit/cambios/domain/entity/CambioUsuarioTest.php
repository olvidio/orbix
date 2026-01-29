<?php

namespace Tests\unit\cambios\domain\entity;

use src\cambios\domain\entity\CambioUsuario;
use src\cambios\domain\value_objects\AvisoTipoId;
use Tests\myTest;

class CambioUsuarioTest extends myTest
{
    private CambioUsuario $CambioUsuario;

    public function setUp(): void
    {
        parent::setUp();
        $this->CambioUsuario = new CambioUsuario();
        $this->CambioUsuario->setId_item(1);
        $this->CambioUsuario->setId_schema_cambio(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->CambioUsuario->setId_item(1);
        $this->assertEquals(1, $this->CambioUsuario->getId_item());
    }

    public function test_set_and_get_id_schema_cambio()
    {
        $this->CambioUsuario->setId_schema_cambio(1);
        $this->assertEquals(1, $this->CambioUsuario->getId_schema_cambio());
    }

    public function test_set_and_get_id_item_cambio()
    {
        $this->CambioUsuario->setId_item_cambio(1);
        $this->assertEquals(1, $this->CambioUsuario->getId_item_cambio());
    }

    public function test_set_and_get_id_usuario()
    {
        $this->CambioUsuario->setId_usuario(1);
        $this->assertEquals(1, $this->CambioUsuario->getId_usuario());
    }

    public function test_set_and_get_sfsv()
    {
        $this->CambioUsuario->setSfsv(1);
        $this->assertEquals(1, $this->CambioUsuario->getSfsv());
    }

    public function test_set_and_get_aviso_tipo()
    {
        $aviso_tipoVo = new AvisoTipoId(1);
        $this->CambioUsuario->setAvisoTipoVo($aviso_tipoVo);
        $this->assertInstanceOf(AvisoTipoId::class, $this->CambioUsuario->getAvisoTipoVo());
        $this->assertEquals(1, $this->CambioUsuario->getAvisoTipoVo()->value());
    }

    public function test_set_and_get_avisado()
    {
        $this->CambioUsuario->setAvisado(true);
        $this->assertTrue($this->CambioUsuario->isAvisado());
    }

    public function test_set_all_attributes()
    {
        $cambioUsuario = new CambioUsuario();
        $attributes = [
            'id_item' => 1,
            'id_schema_cambio' => 1,
            'id_item_cambio' => 1,
            'id_usuario' => 1,
            'sfsv' => 1,
            'aviso_tipo' => new AvisoTipoId(1),
            'avisado' => true,
        ];
        $cambioUsuario->setAllAttributes($attributes);

        $this->assertEquals(1, $cambioUsuario->getId_item());
        $this->assertEquals(1, $cambioUsuario->getId_schema_cambio());
        $this->assertEquals(1, $cambioUsuario->getId_item_cambio());
        $this->assertEquals(1, $cambioUsuario->getId_usuario());
        $this->assertEquals(1, $cambioUsuario->getSfsv());
        $this->assertEquals(1, $cambioUsuario->getAvisoTipoVo()->value());
        $this->assertTrue($cambioUsuario->isAvisado());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $cambioUsuario = new CambioUsuario();
        $attributes = [
            'id_item' => 1,
            'id_schema_cambio' => 1,
            'id_item_cambio' => 1,
            'id_usuario' => 1,
            'sfsv' => 1,
            'aviso_tipo' => 1,
            'avisado' => true,
        ];
        $cambioUsuario->setAllAttributes($attributes);

        $this->assertEquals(1, $cambioUsuario->getId_item());
        $this->assertEquals(1, $cambioUsuario->getId_schema_cambio());
        $this->assertEquals(1, $cambioUsuario->getId_item_cambio());
        $this->assertEquals(1, $cambioUsuario->getId_usuario());
        $this->assertEquals(1, $cambioUsuario->getSfsv());
        $this->assertEquals(1, $cambioUsuario->getAvisoTipoVo()->value());
        $this->assertTrue($cambioUsuario->isAvisado());
    }
}
