<?php

namespace Tests\unit\actividadplazas\domain\entity;

use src\actividadplazas\domain\entity\PlazaPeticion;
use src\actividadplazas\domain\value_objects\PeticionOrden;
use src\actividadplazas\domain\value_objects\PeticionTipo;
use Tests\myTest;

class PlazaPeticionTest extends myTest
{
    private PlazaPeticion $PlazaPeticion;

    public function setUp(): void
    {
        parent::setUp();
        $this->PlazaPeticion = new PlazaPeticion();
        $this->PlazaPeticion->setId_nom(1);
        $this->PlazaPeticion->setId_activ(1);
    }

    public function test_set_and_get_id_nom()
    {
        $this->PlazaPeticion->setId_nom(1);
        $this->assertEquals(1, $this->PlazaPeticion->getId_nom());
    }

    public function test_set_and_get_id_activ()
    {
        $this->PlazaPeticion->setId_activ(1);
        $this->assertEquals(1, $this->PlazaPeticion->getId_activ());
    }

    public function test_set_and_get_orden()
    {
        $ordenVo = new PeticionOrden(1);
        $this->PlazaPeticion->setOrdenVo($ordenVo);
        $this->assertInstanceOf(PeticionOrden::class, $this->PlazaPeticion->getOrdenVo());
        $this->assertEquals(1, $this->PlazaPeticion->getOrdenVo()->value());
    }

    public function test_set_and_get_tipo()
    {
        $tipoVo = new PeticionTipo('test');
        $this->PlazaPeticion->setTipoVo($tipoVo);
        $this->assertInstanceOf(PeticionTipo::class, $this->PlazaPeticion->getTipoVo());
        $this->assertEquals('test', $this->PlazaPeticion->getTipoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $plazaPeticion = new PlazaPeticion();
        $attributes = [
            'id_nom' => 1,
            'id_activ' => 1,
            'orden' => new PeticionOrden(1),
            'tipo' => new PeticionTipo('test'),
        ];
        $plazaPeticion->setAllAttributes($attributes);

        $this->assertEquals(1, $plazaPeticion->getId_nom());
        $this->assertEquals(1, $plazaPeticion->getId_activ());
        $this->assertEquals(1, $plazaPeticion->getOrdenVo()->value());
        $this->assertEquals('test', $plazaPeticion->getTipoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $plazaPeticion = new PlazaPeticion();
        $attributes = [
            'id_nom' => 1,
            'id_activ' => 1,
            'orden' => 1,
            'tipo' => 'test',
        ];
        $plazaPeticion->setAllAttributes($attributes);

        $this->assertEquals(1, $plazaPeticion->getId_nom());
        $this->assertEquals(1, $plazaPeticion->getId_activ());
        $this->assertEquals(1, $plazaPeticion->getOrdenVo()->value());
        $this->assertEquals('test', $plazaPeticion->getTipoVo()->value());
    }
}
