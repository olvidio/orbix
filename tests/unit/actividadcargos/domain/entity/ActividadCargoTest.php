<?php

namespace Tests\unit\actividadcargos\domain\entity;

use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\domain\value_objects\ObservacionesCargo;
use Tests\myTest;

class ActividadCargoTest extends myTest
{
    private ActividadCargo $ActividadCargo;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActividadCargo = new ActividadCargo();
        $this->ActividadCargo->setId_schema(1001);
        $this->ActividadCargo->setId_activ(1);
    }

    public function test_set_and_get_id_activ()
    {
        $this->ActividadCargo->setId_activ(1);
        $this->assertEquals(1, $this->ActividadCargo->getId_activ());
    }

    public function test_set_and_get_id_cargo()
    {
        $this->ActividadCargo->setId_cargo(1);
        $this->assertEquals(1, $this->ActividadCargo->getId_cargo());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ActividadCargo->setId_nom(1);
        $this->assertEquals(1, $this->ActividadCargo->getId_nom());
    }

    public function test_set_and_get_puede_agd()
    {
        $this->ActividadCargo->setPuede_agd(true);
        $this->assertTrue($this->ActividadCargo->isPuede_agd());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservacionesCargo('test');
        $this->ActividadCargo->setObservVo($observVo);
        $this->assertInstanceOf(ObservacionesCargo::class, $this->ActividadCargo->getObservVo());
        $this->assertEquals('test', $this->ActividadCargo->getObservVo()->value());
    }

    public function test_set_and_get_id_item()
    {
        $this->ActividadCargo->setId_item(1);
        $this->assertEquals(1, $this->ActividadCargo->getId_item());
    }

    public function test_set_all_attributes()
    {
        $actividadCargo = new ActividadCargo();
        $attributes = [
            'id_schema' => 1,
            'id_activ' => 1,
            'id_cargo' => 1,
            'id_nom' => 1,
            'puede_agd' => true,
            'observ' => new ObservacionesCargo('test'),
            'id_item' => 1,
        ];
        $actividadCargo->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadCargo->getId_activ());
        $this->assertEquals(1, $actividadCargo->getId_cargo());
        $this->assertEquals(1, $actividadCargo->getId_nom());
        $this->assertTrue($actividadCargo->isPuede_agd());
        $this->assertEquals('test', $actividadCargo->getObservVo()->value());
        $this->assertEquals(1, $actividadCargo->getId_item());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actividadCargo = new ActividadCargo();
        $attributes = [
            'id_schema' => 1,
            'id_activ' => 1,
            'id_cargo' => 1,
            'id_nom' => 1,
            'puede_agd' => true,
            'observ' => 'test',
            'id_item' => 1,
        ];
        $actividadCargo->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadCargo->getId_activ());
        $this->assertEquals(1, $actividadCargo->getId_cargo());
        $this->assertEquals(1, $actividadCargo->getId_nom());
        $this->assertTrue($actividadCargo->isPuede_agd());
        $this->assertEquals('test', $actividadCargo->getObservVo()->value());
        $this->assertEquals(1, $actividadCargo->getId_item());
    }
}
