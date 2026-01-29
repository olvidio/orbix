<?php

namespace Tests\unit\actividadescentro\domain\entity;

use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadescentro\domain\value_objects\CentroEncargadoOrden;
use src\actividadescentro\domain\value_objects\CentroEncargadoTexto;
use Tests\myTest;

class CentroEncargadoTest extends myTest
{
    private CentroEncargado $CentroEncargado;

    public function setUp(): void
    {
        parent::setUp();
        $this->CentroEncargado = new CentroEncargado();
        $this->CentroEncargado->setId_activ(1);
        $this->CentroEncargado->setId_ubi(1);
    }

    public function test_set_and_get_id_activ()
    {
        $this->CentroEncargado->setId_activ(1);
        $this->assertEquals(1, $this->CentroEncargado->getId_activ());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->CentroEncargado->setId_ubi(1);
        $this->assertEquals(1, $this->CentroEncargado->getId_ubi());
    }

    public function test_set_and_get_num_orden()
    {
        $num_ordenVo = new CentroEncargadoOrden(1);
        $this->CentroEncargado->setNumOrdenVo($num_ordenVo);
        $this->assertInstanceOf(CentroEncargadoOrden::class, $this->CentroEncargado->getNumOrdenVo());
        $this->assertEquals(1, $this->CentroEncargado->getNumOrdenVo()->value());
    }

    public function test_set_and_get_encargo()
    {
        $encargoVo = new CentroEncargadoTexto('Test');
        $this->CentroEncargado->setEncargoVo($encargoVo);
        $this->assertInstanceOf(CentroEncargadoTexto::class, $this->CentroEncargado->getEncargoVo());
        $this->assertEquals('Test', $this->CentroEncargado->getEncargoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $centroEncargado = new CentroEncargado();
        $attributes = [
            'id_activ' => 1,
            'id_ubi' => 1,
            'num_orden' => new CentroEncargadoOrden(1),
            'encargo' => new CentroEncargadoTexto('Test'),
        ];
        $centroEncargado->setAllAttributes($attributes);

        $this->assertEquals(1, $centroEncargado->getId_activ());
        $this->assertEquals(1, $centroEncargado->getId_ubi());
        $this->assertEquals(1, $centroEncargado->getNumOrdenVo()->value());
        $this->assertEquals('Test', $centroEncargado->getEncargoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $centroEncargado = new CentroEncargado();
        $attributes = [
            'id_activ' => 1,
            'id_ubi' => 1,
            'num_orden' => 1,
            'encargo' => 'Test',
        ];
        $centroEncargado->setAllAttributes($attributes);

        $this->assertEquals(1, $centroEncargado->getId_activ());
        $this->assertEquals(1, $centroEncargado->getId_ubi());
        $this->assertEquals(1, $centroEncargado->getNumOrdenVo()->value());
        $this->assertEquals('Test', $centroEncargado->getEncargoVo()->value());
    }
}
