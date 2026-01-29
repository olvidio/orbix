<?php

namespace Tests\unit\menus\domain\entity;

use src\menus\domain\entity\GrupMenu;
use src\menus\domain\value_objects\GrupMenuName;
use Tests\myTest;

class GrupMenuTest extends myTest
{
    private GrupMenu $GrupMenu;

    public function setUp(): void
    {
        parent::setUp();
        $this->GrupMenu = new GrupMenu();
        $this->GrupMenu->setId_grupmenu(1);
        $this->GrupMenu->setGrupMenuVo(new GrupMenuName('Test Name'));
    }

    public function test_get_id_grupmenu()
    {
        $this->assertEquals(1, $this->GrupMenu->getId_grupmenu());
    }

    public function test_set_and_get_grup_menu()
    {
        $grup_menuVo = new GrupMenuName('Test Name');
        $this->GrupMenu->setGrupMenuVo($grup_menuVo);
        $this->assertInstanceOf(GrupMenuName::class, $this->GrupMenu->getGrupMenuVo());
        $this->assertEquals('Test Name', $this->GrupMenu->getGrupMenuVo()->value());
    }

    public function test_set_and_get_orden()
    {
        $this->GrupMenu->setOrden(1);
        $this->assertEquals(1, $this->GrupMenu->getOrden());
    }

    public function test_set_all_attributes()
    {
        $grupMenu = new GrupMenu();
        $attributes = [
            'id_grupmenu' => 1,
            'grup_menu' => new GrupMenuName('Test Name'),
            'orden' => 1,
        ];
        $grupMenu->setAllAttributes($attributes);

        $this->assertEquals(1, $grupMenu->getId_grupmenu());
        $this->assertEquals('Test Name', $grupMenu->getGrupMenuVo()->value());
        $this->assertEquals(1, $grupMenu->getOrden());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $grupMenu = new GrupMenu();
        $attributes = [
            'id_grupmenu' => 1,
            'grup_menu' => 'Test Name',
            'orden' => 1,
        ];
        $grupMenu->setAllAttributes($attributes);

        $this->assertEquals(1, $grupMenu->getId_grupmenu());
        $this->assertEquals('Test Name', $grupMenu->getGrupMenuVo()->value());
        $this->assertEquals(1, $grupMenu->getOrden());
    }
}
