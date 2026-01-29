<?php

namespace Tests\unit\actividadcargos\domain\entity;

use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\domain\value_objects\CargoCode;
use src\actividadcargos\domain\value_objects\OrdenCargo;
use src\actividadcargos\domain\value_objects\TipoCargoCode;
use Tests\myTest;

class CargoTest extends myTest
{
    private Cargo $Cargo;

    public function setUp(): void
    {
        parent::setUp();
        $this->Cargo = new Cargo();
        $this->Cargo->setId_cargo(1);
        $this->Cargo->setCargoVo(new CargoCode('Test'));
    }

    public function test_get_id_cargo()
    {
        $this->assertEquals(1, $this->Cargo->getId_cargo());
    }

    public function test_set_and_get_cargo()
    {
        $cargoVo = new CargoCode('Test');
        $this->Cargo->setCargoVo($cargoVo);
        $this->assertInstanceOf(CargoCode::class, $this->Cargo->getCargoVo());
        $this->assertEquals('Test', $this->Cargo->getCargoVo()->value());
    }

    public function test_set_and_get_ordenCargo()
    {
        $ordenCargoVo = new OrdenCargo(1);
        $this->Cargo->setOrdenCargoVo($ordenCargoVo);
        $this->assertInstanceOf(OrdenCargo::class, $this->Cargo->getOrdenCargoVo());
        $this->assertEquals(1, $this->Cargo->getOrdenCargoVo()->value());
    }

    public function test_set_and_get_tipoCargo()
    {
        $tipoCargoVo = new TipoCargoCode('d');
        $this->Cargo->setTipoCargoVo($tipoCargoVo);
        $this->assertInstanceOf(TipoCargoCode::class, $this->Cargo->getTipoCargoVo());
        $this->assertEquals('d', $this->Cargo->getTipoCargoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $cargo = new Cargo();
        $attributes = [
            'id_cargo' => 1,
            'cargo' => new CargoCode('Test'),
            'ordenCargo' => new OrdenCargo(1),
            'tipoCargo' => new TipoCargoCode('d'),
        ];
        $cargo->setAllAttributes($attributes);

        $this->assertEquals(1, $cargo->getId_cargo());
        $this->assertEquals('Test', $cargo->getCargoVo()->value());
        $this->assertEquals(1, $cargo->getOrdenCargoVo()->value());
        $this->assertEquals('d', $cargo->getTipoCargoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $cargo = new Cargo();
        $attributes = [
            'id_cargo' => 1,
            'cargo' => 'Test',
            'ordenCargo' => 1,
            'tipoCargo' => 'd',
        ];
        $cargo->setAllAttributes($attributes);

        $this->assertEquals(1, $cargo->getId_cargo());
        $this->assertEquals('Test', $cargo->getCargoVo()->value());
        $this->assertEquals(1, $cargo->getOrdenCargoVo()->value());
        $this->assertEquals('d', $cargo->getTipoCargoVo()->value());
    }
}
