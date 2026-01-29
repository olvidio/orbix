<?php

namespace Tests\unit\cambios\domain\entity;

use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use src\cambios\domain\value_objects\OperadorPref;
use src\cambios\domain\value_objects\PropiedadNombre;
use Tests\myTest;

class CambioUsuarioPropiedadPrefTest extends myTest
{
    private CambioUsuarioPropiedadPref $CambioUsuarioPropiedadPref;

    public function setUp(): void
    {
        parent::setUp();
        $this->CambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref();
        $this->CambioUsuarioPropiedadPref->setId_item(1);
        $this->CambioUsuarioPropiedadPref->setId_item_usuario_objeto(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->CambioUsuarioPropiedadPref->setId_item(1);
        $this->assertEquals(1, $this->CambioUsuarioPropiedadPref->getId_item());
    }

    public function test_set_and_get_id_item_usuario_objeto()
    {
        $this->CambioUsuarioPropiedadPref->setId_item_usuario_objeto(1);
        $this->assertEquals(1, $this->CambioUsuarioPropiedadPref->getId_item_usuario_objeto());
    }

    public function test_set_and_get_propiedad()
    {
        $propiedadVo = new PropiedadNombre('Test Name');
        $this->CambioUsuarioPropiedadPref->setPropiedadVo($propiedadVo);
        $this->assertInstanceOf(PropiedadNombre::class, $this->CambioUsuarioPropiedadPref->getPropiedadVo());
        $this->assertEquals('Test Name', $this->CambioUsuarioPropiedadPref->getPropiedadVo()->value());
    }

    public function test_set_and_get_valor()
    {
        $this->CambioUsuarioPropiedadPref->setValor('test');
        $this->assertEquals('test', $this->CambioUsuarioPropiedadPref->getValor());
    }

    public function test_set_and_get_valor_old()
    {
        $this->CambioUsuarioPropiedadPref->setValor_old(true);
        $this->assertTrue($this->CambioUsuarioPropiedadPref->isValor_old());
    }

    public function test_set_and_get_valor_new()
    {
        $this->CambioUsuarioPropiedadPref->setValor_new(true);
        $this->assertTrue($this->CambioUsuarioPropiedadPref->isValor_new());
    }

    public function test_set_all_attributes()
    {
        $cambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref();
        $attributes = [
            'id_item' => 1,
            'id_item_usuario_objeto' => 1,
            'propiedad' => new PropiedadNombre('Test Name'),
            'valor' => 'test',
            'valor_old' => true,
            'valor_new' => true,
        ];
        $cambioUsuarioPropiedadPref->setAllAttributes($attributes);

        $this->assertEquals(1, $cambioUsuarioPropiedadPref->getId_item());
        $this->assertEquals(1, $cambioUsuarioPropiedadPref->getId_item_usuario_objeto());
        $this->assertEquals('Test Name', $cambioUsuarioPropiedadPref->getPropiedadVo()->value());
        $this->assertEquals('test', $cambioUsuarioPropiedadPref->getValor());
        $this->assertTrue($cambioUsuarioPropiedadPref->isValor_old());
        $this->assertTrue($cambioUsuarioPropiedadPref->isValor_new());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $cambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref();
        $attributes = [
            'id_item' => 1,
            'id_item_usuario_objeto' => 1,
            'propiedad' => 'Test Name',
            'valor' => 'test',
            'valor_old' => true,
            'valor_new' => true,
        ];
        $cambioUsuarioPropiedadPref->setAllAttributes($attributes);

        $this->assertEquals(1, $cambioUsuarioPropiedadPref->getId_item());
        $this->assertEquals(1, $cambioUsuarioPropiedadPref->getId_item_usuario_objeto());
        $this->assertEquals('Test Name', $cambioUsuarioPropiedadPref->getPropiedadVo()->value());
        $this->assertEquals('test', $cambioUsuarioPropiedadPref->getValor());
        $this->assertTrue($cambioUsuarioPropiedadPref->isValor_old());
        $this->assertTrue($cambioUsuarioPropiedadPref->isValor_new());
    }
}
