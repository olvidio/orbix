<?php

namespace Tests\unit\inventario\domain\entity;

use src\inventario\domain\entity\Lugar;
use src\inventario\domain\value_objects\LugarId;
use src\inventario\domain\value_objects\LugarName;
use Tests\myTest;

class LugarTest extends myTest
{
    private Lugar $Lugar;

    public function setUp(): void
    {
        parent::setUp();
        $this->Lugar = new Lugar();
        $this->Lugar->setId_lugar(1);
        $this->Lugar->setId_ubi(1);
    }

    public function test_get_id_lugar()
    {
        $this->assertEquals(1, $this->Lugar->getId_lugar());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->Lugar->setId_ubi(1);
        $this->assertEquals(1, $this->Lugar->getId_ubi());
    }

    public function test_set_and_get_nom_lugar()
    {
        $nom_lugarVo = new LugarName('Test value');
        $this->Lugar->setNomLugarVo($nom_lugarVo);
        $this->assertInstanceOf(LugarName::class, $this->Lugar->getNomLugarVo());
        $this->assertEquals('Test value', $this->Lugar->getNomLugarVo()->value());
    }

    public function test_set_all_attributes()
    {
        $lugar = new Lugar();
        $attributes = [
            'id_lugar' => 1,
            'id_ubi' => 1,
            'nom_lugar' => new LugarName('Test value'),
        ];
        $lugar->setAllAttributes($attributes);

        $this->assertEquals(1, $lugar->getId_lugar());
        $this->assertEquals(1, $lugar->getId_ubi());
        $this->assertEquals('Test value', $lugar->getNomLugarVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $lugar = new Lugar();
        $attributes = [
            'id_lugar' => 1,
            'id_ubi' => 1,
            'nom_lugar' => 'Test value',
        ];
        $lugar->setAllAttributes($attributes);

        $this->assertEquals(1, $lugar->getId_lugar());
        $this->assertEquals(1, $lugar->getId_ubi());
        $this->assertEquals('Test value', $lugar->getNomLugarVo()->value());
    }
}
