<?php

namespace Tests\unit\misas\domain\entity;

use src\misas\domain\entity\Plantilla;
use Tests\myTest;

class PlantillaTest extends myTest
{
    private Plantilla $Plantilla;

    public function setUp(): void
    {
        parent::setUp();
        $this->Plantilla = new Plantilla();
        $this->Plantilla->setId_item(1);
        $this->Plantilla->setId_ctr(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->Plantilla->setId_item(1);
        $this->assertEquals(1, $this->Plantilla->getId_item());
    }

    public function test_set_and_get_id_ctr()
    {
        $this->Plantilla->setId_ctr(1);
        $this->assertEquals(1, $this->Plantilla->getId_ctr());
    }

    public function test_set_and_get_tarea()
    {
        $this->Plantilla->setTarea(1);
        $this->assertEquals(1, $this->Plantilla->getTarea());
    }

    public function test_set_and_get_dia()
    {
        $this->Plantilla->setDia('test');
        $this->assertEquals('test', $this->Plantilla->getDia());
    }

    public function test_set_and_get_semana()
    {
        $this->Plantilla->setSemana(1);
        $this->assertEquals(1, $this->Plantilla->getSemana());
    }

    public function test_set_and_get_id_nom()
    {
        $this->Plantilla->setId_nom(1);
        $this->assertEquals(1, $this->Plantilla->getId_nom());
    }

    public function test_set_and_get_observ()
    {
        $this->Plantilla->setObserv('test');
        $this->assertEquals('test', $this->Plantilla->getObserv());
    }

    public function test_set_all_attributes()
    {
        $plantilla = new Plantilla();
        $attributes = [
            'id_item' => 1,
            'id_ctr' => 1,
            'tarea' => 1,
            'dia' => 'test',
            'semana' => 1,
            'id_nom' => 1,
            'observ' => 'test',
        ];
        $plantilla->setAllAttributes($attributes);

        $this->assertEquals(1, $plantilla->getId_item());
        $this->assertEquals(1, $plantilla->getId_ctr());
        $this->assertEquals(1, $plantilla->getTarea());
        $this->assertEquals('test', $plantilla->getDia());
        $this->assertEquals(1, $plantilla->getSemana());
        $this->assertEquals(1, $plantilla->getId_nom());
        $this->assertEquals('test', $plantilla->getObserv());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $plantilla = new Plantilla();
        $attributes = [
            'id_item' => 1,
            'id_ctr' => 1,
            'tarea' => 1,
            'dia' => 'test',
            'semana' => 1,
            'id_nom' => 1,
            'observ' => 'test',
        ];
        $plantilla->setAllAttributes($attributes);

        $this->assertEquals(1, $plantilla->getId_item());
        $this->assertEquals(1, $plantilla->getId_ctr());
        $this->assertEquals(1, $plantilla->getTarea());
        $this->assertEquals('test', $plantilla->getDia());
        $this->assertEquals(1, $plantilla->getSemana());
        $this->assertEquals(1, $plantilla->getId_nom());
        $this->assertEquals('test', $plantilla->getObserv());
    }
}
