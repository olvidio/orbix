<?php

namespace Tests\unit\configuracion\domain\entity;

use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModsReq;
use src\configuracion\domain\value_objects\ModuloDescription;
use src\configuracion\domain\value_objects\ModuloId;
use src\configuracion\domain\value_objects\ModuloName;
use Tests\myTest;

class ModuloTest extends myTest
{
    private Modulo $Modulo;

    public function setUp(): void
    {
        parent::setUp();
        $this->Modulo = new Modulo();
        $this->Modulo->setIdModVo(new ModuloId(1));
        $this->Modulo->setNomVo(new ModuloName('Test Name'));
    }

    public function test_set_and_get_id_mod()
    {
        $id_modVo = new ModuloId(1);
        $this->Modulo->setIdModVo($id_modVo);
        $this->assertInstanceOf(ModuloId::class, $this->Modulo->getIdModVo());
        $this->assertEquals(1, $this->Modulo->getIdModVo()->value());
    }

    public function test_set_and_get_nom()
    {
        $nomVo = new ModuloName('Test Name');
        $this->Modulo->setNomVo($nomVo);
        $this->assertInstanceOf(ModuloName::class, $this->Modulo->getNomVo());
        $this->assertEquals('Test Name', $this->Modulo->getNomVo()->value());
    }

    public function test_set_and_get_descripcion()
    {
        $descripcionVo = new ModuloDescription('test');
        $this->Modulo->setDescripcionVo($descripcionVo);
        $this->assertInstanceOf(ModuloDescription::class, $this->Modulo->getDescripcionVo());
        $this->assertEquals('test', $this->Modulo->getDescripcionVo()->value());
    }

    public function test_set_and_get_mods_req()
    {
        $mods_reqVo = new ModsReq(['test']);
        $this->Modulo->setModsReqVo($mods_reqVo);
        $this->assertInstanceOf(ModsReq::class, $this->Modulo->getModsReqVo());
        $this->assertEquals(['test'], $this->Modulo->getModsReqVo()->toArray());
    }

    public function test_set_and_get_apps_req()
    {
        $apps_reqVo = new AppsReq('test');
        $this->Modulo->setAppsReqVo($apps_reqVo);
        $this->assertInstanceOf(AppsReq::class, $this->Modulo->getAppsReqVo());
        $this->assertEquals(['test'], $this->Modulo->getAppsReqVo()->toArray());
    }

    public function test_set_all_attributes()
    {
        $modulo = new Modulo();
        $attributes = [
            'id_mod' => new ModuloId(1),
            'nom' => new ModuloName('Test Name'),
            'descripcion' => new ModuloDescription('test'),
            'mods_req' => new ModsReq(['test']),
            'apps_req' => new AppsReq(['test']),
        ];
        $modulo->setAllAttributes($attributes);

        $this->assertEquals(1, $modulo->getIdModVo()->value());
        $this->assertEquals('Test Name', $modulo->getNomVo()->value());
        $this->assertEquals('test', $modulo->getDescripcionVo()->value());
        $this->assertEquals(['test'], $modulo->getModsReqVo()->toArray());
        $this->assertEquals(['test'], $modulo->getAppsReqVo()->toArray());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $modulo = new Modulo();
        $attributes = [
            'id_mod' => 1,
            'nom' => 'Test Name',
            'descripcion' => 'test',
            'mods_req' => ['test'],
            'apps_req' => ['test'],
        ];
        $modulo->setAllAttributes($attributes);

        $this->assertEquals(1, $modulo->getIdModVo()->value());
        $this->assertEquals('Test Name', $modulo->getNomVo()->value());
        $this->assertEquals('test', $modulo->getDescripcionVo()->value());
        $this->assertEquals(['test'], $modulo->getModsReqVo()->toArray());
        $this->assertEquals(['test'], $modulo->getAppsReqVo()->toArray());
    }
}
