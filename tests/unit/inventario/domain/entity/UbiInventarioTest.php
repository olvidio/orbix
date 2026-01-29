<?php

namespace Tests\unit\inventario\domain\entity;

use src\inventario\domain\entity\UbiInventario;
use src\inventario\domain\value_objects\UbiInventarioId;
use src\inventario\domain\value_objects\UbiInventarioIdActiv;
use src\inventario\domain\value_objects\UbiInventarioName;
use Tests\myTest;

class UbiInventarioTest extends myTest
{
    private UbiInventario $UbiInventario;

    public function setUp(): void
    {
        parent::setUp();
        $this->UbiInventario = new UbiInventario();
        $this->UbiInventario->setIdUbiVo(new UbiInventarioId(1));
        $this->UbiInventario->setNomUbiVo(new UbiInventarioName('Test value'));
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new UbiInventarioId(1);
        $this->UbiInventario->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(UbiInventarioId::class, $this->UbiInventario->getIdUbiVo());
        $this->assertEquals(1, $this->UbiInventario->getIdUbiVo()->value());
    }

    public function test_set_and_get_nom_ubi()
    {
        $nom_ubiVo = new UbiInventarioName('Test value');
        $this->UbiInventario->setNomUbiVo($nom_ubiVo);
        $this->assertInstanceOf(UbiInventarioName::class, $this->UbiInventario->getNomUbiVo());
        $this->assertEquals('Test value', $this->UbiInventario->getNomUbiVo()->value());
    }

    public function test_set_and_get_id_ubi_activ()
    {
        $id_ubi_activVo = new UbiInventarioIdActiv(1);
        $this->UbiInventario->setIdUbiActivVo($id_ubi_activVo);
        $this->assertInstanceOf(UbiInventarioIdActiv::class, $this->UbiInventario->getIdUbiActivVo());
        $this->assertEquals(1, $this->UbiInventario->getIdUbiActivVo()->value());
    }

    public function test_set_all_attributes()
    {
        $ubiInventario = new UbiInventario();
        $attributes = [
            'id_ubi' => new UbiInventarioId(1),
            'nom_ubi' => new UbiInventarioName('Test value'),
            'id_ubi_activ' => new UbiInventarioIdActiv(1),
        ];
        $ubiInventario->setAllAttributes($attributes);

        $this->assertEquals(1, $ubiInventario->getIdUbiVo()->value());
        $this->assertEquals('Test value', $ubiInventario->getNomUbiVo()->value());
        $this->assertEquals(1, $ubiInventario->getIdUbiActivVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $ubiInventario = new UbiInventario();
        $attributes = [
            'id_ubi' => 1,
            'nom_ubi' => 'Test value',
            'id_ubi_activ' => 1,
        ];
        $ubiInventario->setAllAttributes($attributes);

        $this->assertEquals(1, $ubiInventario->getIdUbiVo()->value());
        $this->assertEquals('Test value', $ubiInventario->getNomUbiVo()->value());
        $this->assertEquals(1, $ubiInventario->getIdUbiActivVo()->value());
    }
}
