<?php

namespace Tests\unit\procesos\domain\entity;

use src\actividades\domain\value_objects\ActividadTipoId;
use src\procesos\domain\entity\PermUsuarioActividad;
use src\procesos\domain\value_objects\FaseId;
use Tests\myTest;

class PermUsuarioActividadTest extends myTest
{
    private PermUsuarioActividad $PermUsuarioActividad;

    public function setUp(): void
    {
        parent::setUp();
        $this->PermUsuarioActividad = new PermUsuarioActividad();
        $this->PermUsuarioActividad->setId_item(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->PermUsuarioActividad->setId_item(1);
        $this->assertEquals(1, $this->PermUsuarioActividad->getId_item());
    }

    public function test_set_and_get_id_usuario()
    {
        $this->PermUsuarioActividad->setId_usuario(1);
        $this->assertEquals(1, $this->PermUsuarioActividad->getId_usuario());
    }

    public function test_set_and_get_dl_propia()
    {
        $this->PermUsuarioActividad->setDl_propia(true);
        $this->assertTrue($this->PermUsuarioActividad->isDl_propia());
    }

    public function test_set_and_get_id_tipo_activ_txt()
    {
        $id_tipo_activ_txtVo = new ActividadTipoId(234567);
        $this->PermUsuarioActividad->setIdTipoActivTxtVo($id_tipo_activ_txtVo);
        $this->assertInstanceOf(ActividadTipoId::class, $this->PermUsuarioActividad->getIdTipoActivTxtVo());
        $this->assertEquals(234567, $this->PermUsuarioActividad->getIdTipoActivTxtVo()->value());
    }

    public function test_set_and_get_fase_ref()
    {
        $fase_refVo = new FaseId(1);
        $this->PermUsuarioActividad->setFaseRefVo($fase_refVo);
        $this->assertInstanceOf(FaseId::class, $this->PermUsuarioActividad->getFaseRefVo());
        $this->assertEquals(1, $this->PermUsuarioActividad->getFaseRefVo()->value());
    }

    public function test_set_and_get_afecta_a()
    {
        $this->PermUsuarioActividad->setAfecta_a(1);
        $this->assertEquals(1, $this->PermUsuarioActividad->getAfecta_a());
    }

    public function test_set_and_get_perm_on()
    {
        $this->PermUsuarioActividad->setPerm_on(1);
        $this->assertEquals(1, $this->PermUsuarioActividad->getPerm_on());
    }

    public function test_set_and_get_perm_off()
    {
        $this->PermUsuarioActividad->setPerm_off(1);
        $this->assertEquals(1, $this->PermUsuarioActividad->getPerm_off());
    }

    public function test_set_all_attributes()
    {
        $permUsuarioActividad = new PermUsuarioActividad();
        $attributes = [
            'id_item' => 1,
            'id_usuario' => 1,
            'dl_propia' => true,
            'id_tipo_activ_txt' => new ActividadTipoId(234567),
            'fase_ref' => new FaseId(1),
            'afecta_a' => 1,
            'perm_on' => 1,
            'perm_off' => 1,
        ];
        $permUsuarioActividad->setAllAttributes($attributes);

        $this->assertEquals(1, $permUsuarioActividad->getId_item());
        $this->assertEquals(1, $permUsuarioActividad->getId_usuario());
        $this->assertTrue($permUsuarioActividad->isDl_propia());
        $this->assertEquals(234567, $permUsuarioActividad->getIdTipoActivTxtVo()->value());
        $this->assertEquals(1, $permUsuarioActividad->getFaseRefVo()->value());
        $this->assertEquals(1, $permUsuarioActividad->getAfecta_a());
        $this->assertEquals(1, $permUsuarioActividad->getPerm_on());
        $this->assertEquals(1, $permUsuarioActividad->getPerm_off());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $permUsuarioActividad = new PermUsuarioActividad();
        $attributes = [
            'id_item' => 1,
            'id_usuario' => 1,
            'dl_propia' => true,
            'id_tipo_activ_txt' => new ActividadTipoId(234567),
            'fase_ref' => 1,
            'afecta_a' => 1,
            'perm_on' => 1,
            'perm_off' => 1,
        ];
        $permUsuarioActividad->setAllAttributes($attributes);

        $this->assertEquals(1, $permUsuarioActividad->getId_item());
        $this->assertEquals(1, $permUsuarioActividad->getId_usuario());
        $this->assertTrue($permUsuarioActividad->isDl_propia());
        $this->assertEquals(234567, $permUsuarioActividad->getIdTipoActivTxtVo()->value());
        $this->assertEquals(1, $permUsuarioActividad->getFaseRefVo()->value());
        $this->assertEquals(1, $permUsuarioActividad->getAfecta_a());
        $this->assertEquals(1, $permUsuarioActividad->getPerm_on());
        $this->assertEquals(1, $permUsuarioActividad->getPerm_off());
    }
}
