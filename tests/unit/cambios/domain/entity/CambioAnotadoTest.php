<?php

namespace Tests\unit\cambios\domain\entity;

use src\cambios\domain\entity\CambioAnotado;
use Tests\myTest;

class CambioAnotadoTest extends myTest
{
    private CambioAnotado $CambioAnotado;

    public function setUp(): void
    {
        parent::setUp();
        $this->CambioAnotado = new CambioAnotado();
        $this->CambioAnotado->setId_item(1);
        $this->CambioAnotado->setId_schema_cambio(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->CambioAnotado->setId_item(1);
        $this->assertEquals(1, $this->CambioAnotado->getId_item());
    }

    public function test_set_and_get_id_schema_cambio()
    {
        $this->CambioAnotado->setId_schema_cambio(1);
        $this->assertEquals(1, $this->CambioAnotado->getId_schema_cambio());
    }

    public function test_set_and_get_id_item_cambio()
    {
        $this->CambioAnotado->setId_item_cambio(1);
        $this->assertEquals(1, $this->CambioAnotado->getId_item_cambio());
    }

    public function test_set_and_get_anotado()
    {
        $this->CambioAnotado->setAnotado(true);
        $this->assertTrue($this->CambioAnotado->isAnotado());
    }

    public function test_set_and_get_server()
    {
        $this->CambioAnotado->setServer(1);
        $this->assertEquals(1, $this->CambioAnotado->getServer());
    }

    public function test_set_all_attributes()
    {
        $cambioAnotado = new CambioAnotado();
        $attributes = [
            'id_item' => 1,
            'id_schema_cambio' => 1,
            'id_item_cambio' => 1,
            'anotado' => true,
            'server' => 1,
        ];
        $cambioAnotado->setAllAttributes($attributes);

        $this->assertEquals(1, $cambioAnotado->getId_item());
        $this->assertEquals(1, $cambioAnotado->getId_schema_cambio());
        $this->assertEquals(1, $cambioAnotado->getId_item_cambio());
        $this->assertTrue($cambioAnotado->isAnotado());
        $this->assertEquals(1, $cambioAnotado->getServer());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $cambioAnotado = new CambioAnotado();
        $attributes = [
            'id_item' => 1,
            'id_schema_cambio' => 1,
            'id_item_cambio' => 1,
            'anotado' => true,
            'server' => 1,
        ];
        $cambioAnotado->setAllAttributes($attributes);

        $this->assertEquals(1, $cambioAnotado->getId_item());
        $this->assertEquals(1, $cambioAnotado->getId_schema_cambio());
        $this->assertEquals(1, $cambioAnotado->getId_item_cambio());
        $this->assertTrue($cambioAnotado->isAnotado());
        $this->assertEquals(1, $cambioAnotado->getServer());
    }
}
