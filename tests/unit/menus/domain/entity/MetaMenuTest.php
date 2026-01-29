<?php

namespace Tests\unit\menus\domain\entity;

use src\menus\domain\entity\MetaMenu;
use src\menus\domain\value_objects\MetaMenuDescripcion;
use src\menus\domain\value_objects\MetaMenuParametros;
use src\menus\domain\value_objects\MetaMenuUrl;
use Tests\myTest;

class MetaMenuTest extends myTest
{
    private MetaMenu $MetaMenu;

    public function setUp(): void
    {
        parent::setUp();
        $this->MetaMenu = new MetaMenu();
        $this->MetaMenu->setId_metamenu(1);
    }

    public function test_get_id_metamenu()
    {
        $this->assertEquals(1, $this->MetaMenu->getId_metamenu());
    }

    public function test_set_and_get_id_mod()
    {
        $this->MetaMenu->setId_mod(1);
        $this->assertEquals(1, $this->MetaMenu->getId_mod());
    }

    public function test_set_and_get_url()
    {
        $this->MetaMenu->setUrl('test');
        $this->assertEquals('test', $this->MetaMenu->getUrl());
    }

    public function test_set_and_get_parametros()
    {
        $parametrosVo = new MetaMenuParametros('test');
        $this->MetaMenu->setParametrosVo($parametrosVo);
        $this->assertInstanceOf(MetaMenuParametros::class, $this->MetaMenu->getParametrosVo());
        $this->assertEquals('test', $this->MetaMenu->getParametrosVo()->value());
    }

    public function test_set_and_get_descripcion()
    {
        $descripcionVo = new MetaMenuDescripcion('test');
        $this->MetaMenu->setDescripcionVo($descripcionVo);
        $this->assertInstanceOf(MetaMenuDescripcion::class, $this->MetaMenu->getDescripcionVo());
        $this->assertEquals('test', $this->MetaMenu->getDescripcionVo()->value());
    }

    public function test_set_all_attributes()
    {
        $metaMenu = new MetaMenu();
        $attributes = [
            'id_metamenu' => 1,
            'id_mod' => 1,
            'url' => 'test',
            'parametros' => new MetaMenuParametros('test'),
            'descripcion' => new MetaMenuDescripcion('test'),
        ];
        $metaMenu->setAllAttributes($attributes);

        $this->assertEquals(1, $metaMenu->getId_metamenu());
        $this->assertEquals(1, $metaMenu->getId_mod());
        $this->assertEquals('test', $metaMenu->getUrl());
        $this->assertEquals('test', $metaMenu->getParametrosVo()->value());
        $this->assertEquals('test', $metaMenu->getDescripcionVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $metaMenu = new MetaMenu();
        $attributes = [
            'id_metamenu' => 1,
            'id_mod' => 1,
            'url' => 'test',
            'parametros' => 'test',
            'descripcion' => 'test',
        ];
        $metaMenu->setAllAttributes($attributes);

        $this->assertEquals(1, $metaMenu->getId_metamenu());
        $this->assertEquals(1, $metaMenu->getId_mod());
        $this->assertEquals('test', $metaMenu->getUrl());
        $this->assertEquals('test', $metaMenu->getParametrosVo()->value());
        $this->assertEquals('test', $metaMenu->getDescripcionVo()->value());
    }
}
