<?php

namespace Tests\unit\inventario\domain\entity;

use src\inventario\domain\entity\Coleccion;
use src\inventario\domain\value_objects\ColeccionAgrupar;
use src\inventario\domain\value_objects\ColeccionId;
use src\inventario\domain\value_objects\ColeccionName;
use Tests\myTest;

class ColeccionTest extends myTest
{
    private Coleccion $Coleccion;

    public function setUp(): void
    {
        parent::setUp();
        $this->Coleccion = new Coleccion();
        $this->Coleccion->setId_coleccion(1);
        $this->Coleccion->setNomColeccionVo(new ColeccionName('Test value'));
    }

    public function test_get_id_coleccion()
    {
        $this->assertEquals(1, $this->Coleccion->getId_coleccion());
    }

    public function test_set_and_get_nom_coleccion()
    {
        $nom_coleccionVo = new ColeccionName('Test value');
        $this->Coleccion->setNomColeccionVo($nom_coleccionVo);
        $this->assertInstanceOf(ColeccionName::class, $this->Coleccion->getNomColeccionVo());
        $this->assertEquals('Test value', $this->Coleccion->getNomColeccionVo()->value());
    }

    public function test_set_and_get_agrupar()
    {
        $this->Coleccion->setAgrupar(true);
        $this->assertTrue($this->Coleccion->isAgrupar());
    }

    public function test_set_all_attributes()
    {
        $coleccion = new Coleccion();
        $attributes = [
            'id_coleccion' => new ColeccionId(1),
            'nom_coleccion' => new ColeccionName('Test value'),
            'agrupar' => true,
        ];
        $coleccion->setAllAttributes($attributes);

        $this->assertEquals(1, $coleccion->getIdColeccionVo()->value());
        $this->assertEquals('Test value', $coleccion->getNomColeccionVo()->value());
        $this->assertTrue($coleccion->isAgrupar());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $coleccion = new Coleccion();
        $attributes = [
            'id_coleccion' => 1,
            'nom_coleccion' => 'Test value',
            'agrupar' => true,
        ];
        $coleccion->setAllAttributes($attributes);

        $this->assertEquals(1, $coleccion->getIdColeccionVo()->value());
        $this->assertEquals('Test value', $coleccion->getNomColeccionVo()->value());
        $this->assertTrue($coleccion->isAgrupar());
    }
}
