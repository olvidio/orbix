<?php

namespace Tests\unit\menus\domain\entity;

use src\menus\domain\entity\MenuDb;
use src\menus\domain\value_objects\MenuName;
use src\menus\domain\value_objects\MenuParametros;
use Tests\myTest;

class MenuDbTest extends myTest
{
    private MenuDb $MenuDb;

    public function setUp(): void
    {
        parent::setUp();
        $this->MenuDb = new MenuDb();
        $this->MenuDb->setId_menu(1);
    }

    public function test_set_and_get_id_menu()
    {
        $this->MenuDb->setId_menu(1);
        $this->assertEquals(1, $this->MenuDb->getId_menu());
    }

    public function test_set_and_get_orden()
    {
        $this->MenuDb->setOrden([]);
        $this->assertEquals([], $this->MenuDb->getOrden());
    }

    public function test_set_and_get_menu()
    {
        $menuVo = new MenuName('Test Name');
        $this->MenuDb->setMenuVo($menuVo);
        $this->assertInstanceOf(MenuName::class, $this->MenuDb->getMenuVo());
        $this->assertEquals('Test Name', $this->MenuDb->getMenuVo()->value());
    }

    public function test_set_and_get_parametros()
    {
        $parametrosVo = new MenuParametros('test');
        $this->MenuDb->setParametrosVo($parametrosVo);
        $this->assertInstanceOf(MenuParametros::class, $this->MenuDb->getParametrosVo());
        $this->assertEquals('test', $this->MenuDb->getParametrosVo()->value());
    }

    public function test_set_and_get_id_metamenu()
    {
        $this->MenuDb->setId_metamenu(1);
        $this->assertEquals(1, $this->MenuDb->getId_metamenu());
    }

    public function test_set_and_get_menu_perm()
    {
        $this->MenuDb->setMenu_perm(1);
        $this->assertEquals(1, $this->MenuDb->getMenu_perm());
    }

    public function test_set_and_get_id_grupmenu()
    {
        $this->MenuDb->setId_grupmenu(1);
        $this->assertEquals(1, $this->MenuDb->getId_grupmenu());
    }

    public function test_set_and_get_ok()
    {
        $this->MenuDb->setOk(true);
        $this->assertTrue($this->MenuDb->isOk());
    }

    public function test_set_all_attributes()
    {
        $menuDb = new MenuDb();
        $attributes = [
            'id_menu' => 1,
            'orden' => [],
            'menu' => new MenuName('Test Name'),
            'parametros' => new MenuParametros('test'),
            'id_metamenu' => 1,
            'menu_perm' => 1,
            'id_grupmenu' => 1,
            'ok' => true,
        ];
        $menuDb->setAllAttributes($attributes);

        $this->assertEquals(1, $menuDb->getId_menu());
        $this->assertEquals([], $menuDb->getOrden());
        $this->assertEquals('Test Name', $menuDb->getMenuVo()->value());
        $this->assertEquals('test', $menuDb->getParametrosVo()->value());
        $this->assertEquals(1, $menuDb->getId_metamenu());
        $this->assertEquals(1, $menuDb->getMenu_perm());
        $this->assertEquals(1, $menuDb->getId_grupmenu());
        $this->assertTrue($menuDb->isOk());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $menuDb = new MenuDb();
        $attributes = [
            'id_menu' => 1,
            'orden' => [],
            'menu' => 'Test Name',
            'parametros' => 'test',
            'id_metamenu' => 1,
            'menu_perm' => 1,
            'id_grupmenu' => 1,
            'ok' => true,
        ];
        $menuDb->setAllAttributes($attributes);

        $this->assertEquals(1, $menuDb->getId_menu());
        $this->assertEquals([], $menuDb->getOrden());
        $this->assertEquals('Test Name', $menuDb->getMenuVo()->value());
        $this->assertEquals('test', $menuDb->getParametrosVo()->value());
        $this->assertEquals(1, $menuDb->getId_metamenu());
        $this->assertEquals(1, $menuDb->getMenu_perm());
        $this->assertEquals(1, $menuDb->getId_grupmenu());
        $this->assertTrue($menuDb->isOk());
    }
}
