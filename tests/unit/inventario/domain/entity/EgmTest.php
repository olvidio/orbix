<?php

namespace Tests\unit\inventario\domain\entity;

use src\inventario\domain\entity\Egm;
use src\inventario\domain\value_objects\EgmEquipajeId;
use src\inventario\domain\value_objects\EgmGrupoId;
use src\inventario\domain\value_objects\EgmLugarId;
use src\inventario\domain\value_objects\EgmTexto;
use Tests\myTest;

class EgmTest extends myTest
{
    private Egm $Egm;

    public function setUp(): void
    {
        parent::setUp();
        $this->Egm = new Egm();
        $this->Egm->setId_item(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->Egm->setId_item(1);
        $this->assertEquals(1, $this->Egm->getId_item());
    }

    public function test_set_and_get_id_equipaje()
    {
        $id_equipajeVo = new EgmEquipajeId(1);
        $this->Egm->setIdEquipajeVo($id_equipajeVo);
        $this->assertInstanceOf(EgmEquipajeId::class, $this->Egm->getIdEquipajeVo());
        $this->assertEquals(1, $this->Egm->getIdEquipajeVo()->value());
    }

    public function test_set_and_get_id_grupo()
    {
        $id_grupoVo = new EgmGrupoId(1);
        $this->Egm->setIdGrupoVo($id_grupoVo);
        $this->assertInstanceOf(EgmGrupoId::class, $this->Egm->getIdGrupoVo());
        $this->assertEquals(1, $this->Egm->getIdGrupoVo()->value());
    }

    public function test_set_and_get_id_lugar()
    {
        $id_lugarVo = new EgmLugarId(1);
        $this->Egm->setIdLugarVo($id_lugarVo);
        $this->assertInstanceOf(EgmLugarId::class, $this->Egm->getIdLugarVo());
        $this->assertEquals(1, $this->Egm->getIdLugarVo()->value());
    }

    public function test_set_and_get_texto()
    {
        $textoVo = new EgmTexto('Test');
        $this->Egm->setTextoVo($textoVo);
        $this->assertInstanceOf(EgmTexto::class, $this->Egm->getTextoVo());
        $this->assertEquals('Test', $this->Egm->getTextoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $egm = new Egm();
        $attributes = [
            'id_item' => 1,
            'id_equipaje' => new EgmEquipajeId(1),
            'id_grupo' => new EgmGrupoId(1),
            'id_lugar' => new EgmLugarId(1),
            'texto' => new EgmTexto('Test'),
        ];
        $egm->setAllAttributes($attributes);

        $this->assertEquals(1, $egm->getId_item());
        $this->assertEquals(1, $egm->getIdEquipajeVo()->value());
        $this->assertEquals(1, $egm->getIdGrupoVo()->value());
        $this->assertEquals(1, $egm->getIdLugarVo()->value());
        $this->assertEquals('Test', $egm->getTextoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $egm = new Egm();
        $attributes = [
            'id_item' => 1,
            'id_equipaje' => 1,
            'id_grupo' => 1,
            'id_lugar' => 1,
            'texto' => 'Test',
        ];
        $egm->setAllAttributes($attributes);

        $this->assertEquals(1, $egm->getId_item());
        $this->assertEquals(1, $egm->getIdEquipajeVo()->value());
        $this->assertEquals(1, $egm->getIdGrupoVo()->value());
        $this->assertEquals(1, $egm->getIdLugarVo()->value());
        $this->assertEquals('Test', $egm->getTextoVo()->value());
    }
}
