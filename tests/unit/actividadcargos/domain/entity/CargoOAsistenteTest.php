<?php

namespace Tests\unit\actividadcargos\domain\entity;

use src\actividadcargos\domain\entity\CargoOAsistente;
use Tests\myTest;

class CargoOAsistenteTest extends myTest
{
    private CargoOAsistente $CargoOAsistente;

    public function setUp(): void
    {
        parent::setUp();
        $this->CargoOAsistente = new CargoOAsistente(54321);
        $this->CargoOAsistente->setId_activ(1);
        $this->CargoOAsistente->setId_nom(1);
    }

    public function test_set_and_get_id_activ()
    {
        $this->CargoOAsistente->setId_activ(54321);
        $this->assertEquals(54321, $this->CargoOAsistente->getId_activ());
    }

    public function test_set_and_get_id_nom()
    {
        $this->CargoOAsistente->setId_nom(1);
        $this->assertEquals(1, $this->CargoOAsistente->getId_nom());
    }

    public function test_set_and_get_propio()
    {
        $this->CargoOAsistente->setPropio(true);
        $this->assertTrue($this->CargoOAsistente->isPropio());
    }

    public function test_set_and_get_id_cargo()
    {
        $this->CargoOAsistente->setId_cargo(1);
        $this->assertEquals(1, $this->CargoOAsistente->getId_cargo());
    }

}
