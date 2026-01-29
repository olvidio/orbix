<?php

namespace Tests\unit\menus\domain\entity;

use src\menus\domain\entity\GrupMenuRole;
use Tests\myTest;

class GrupMenuRoleTest extends myTest
{
    private GrupMenuRole $GrupMenuRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->GrupMenuRole = new GrupMenuRole();
        $this->GrupMenuRole->setId_item(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->GrupMenuRole->setId_item(1);
        $this->assertEquals(1, $this->GrupMenuRole->getId_item());
    }

    public function test_set_and_get_id_grupmenu()
    {
        $this->GrupMenuRole->setId_grupmenu(1);
        $this->assertEquals(1, $this->GrupMenuRole->getId_grupmenu());
    }

    public function test_set_and_get_id_role()
    {
        $this->GrupMenuRole->setId_role(1);
        $this->assertEquals(1, $this->GrupMenuRole->getId_role());
    }

    public function test_set_all_attributes()
    {
        $grupMenuRole = new GrupMenuRole();
        $attributes = [
            'id_item' => 1,
            'id_grupmenu' => 1,
            'id_role' => 1,
        ];
        $grupMenuRole->setAllAttributes($attributes);

        $this->assertEquals(1, $grupMenuRole->getId_item());
        $this->assertEquals(1, $grupMenuRole->getId_grupmenu());
        $this->assertEquals(1, $grupMenuRole->getId_role());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $grupMenuRole = new GrupMenuRole();
        $attributes = [
            'id_item' => 1,
            'id_grupmenu' => 1,
            'id_role' => 1,
        ];
        $grupMenuRole->setAllAttributes($attributes);

        $this->assertEquals(1, $grupMenuRole->getId_item());
        $this->assertEquals(1, $grupMenuRole->getId_grupmenu());
        $this->assertEquals(1, $grupMenuRole->getId_role());
    }
}
