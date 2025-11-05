<?php

namespace Tests\unit\usuarios\domain\entity;

use src\usuarios\domain\entity\PermMenu;
use Tests\myTest;

class PermMenuTest extends myTest
{
    private PermMenu $permMenu;

    public function setUp(): void
    {
        parent::setUp();
        $this->permMenu = new PermMenu();
        $this->permMenu->setId_item(1);
        $this->permMenu->setId_usuario(2);
    }

    public function test_get_id_item()
    {
        $this->assertEquals(1, $this->permMenu->getId_item());
    }

    public function test_set_and_get_id_item()
    {
        $this->permMenu->setId_item(3);
        $this->assertEquals(3, $this->permMenu->getId_item());
    }

    public function test_get_id_usuario()
    {
        $this->assertEquals(2, $this->permMenu->getId_usuario());
    }

    public function test_set_and_get_id_usuario()
    {
        $this->permMenu->setId_usuario(4);
        $this->assertEquals(4, $this->permMenu->getId_usuario());
    }

    public function test_get_menu_perm()
    {
        $this->assertNull($this->permMenu->getMenu_perm());
    }

    public function test_set_and_get_menu_perm()
    {
        $this->permMenu->setMenu_perm(5);
        $this->assertEquals(5, $this->permMenu->getMenu_perm());
    }

    public function test_set_all_attributes()
    {
        $permMenu = new PermMenu();
        $attributes = [
            'id_item' => 1,
            'id_usuario' => 2,
            'menu_perm' => 5
        ];
        $permMenu->setAllAttributes($attributes);

        $this->assertEquals(1, $permMenu->getId_item());
        $this->assertEquals(2, $permMenu->getId_usuario());
        $this->assertEquals(5, $permMenu->getMenu_perm());
    }
}