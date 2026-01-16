<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\GrupMenuRoleGrupMenuId;
use Tests\myTest;

class GrupMenuRoleGrupMenuIdTest extends myTest
{
    public function test_create_valid_grupMenuRoleGrupMenuId()
    {
        $grupMenuRoleGrupMenuId = new GrupMenuRoleGrupMenuId(123);
        $this->assertEquals(123, $grupMenuRoleGrupMenuId->value());
    }

    public function test_equals_returns_true_for_same_grupMenuRoleGrupMenuId()
    {
        $grupMenuRoleGrupMenuId1 = new GrupMenuRoleGrupMenuId(123);
        $grupMenuRoleGrupMenuId2 = new GrupMenuRoleGrupMenuId(123);
        $this->assertTrue($grupMenuRoleGrupMenuId1->equals($grupMenuRoleGrupMenuId2));
    }

    public function test_equals_returns_false_for_different_grupMenuRoleGrupMenuId()
    {
        $grupMenuRoleGrupMenuId1 = new GrupMenuRoleGrupMenuId(123);
        $grupMenuRoleGrupMenuId2 = new GrupMenuRoleGrupMenuId(456);
        $this->assertFalse($grupMenuRoleGrupMenuId1->equals($grupMenuRoleGrupMenuId2));
    }

    public function test_to_string_returns_grupMenuRoleGrupMenuId_value()
    {
        $grupMenuRoleGrupMenuId = new GrupMenuRoleGrupMenuId(123);
        $this->assertEquals(123, (string)$grupMenuRoleGrupMenuId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $grupMenuRoleGrupMenuId = GrupMenuRoleGrupMenuId::fromNullableInt(123);
        $this->assertInstanceOf(GrupMenuRoleGrupMenuId::class, $grupMenuRoleGrupMenuId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $grupMenuRoleGrupMenuId = GrupMenuRoleGrupMenuId::fromNullableInt(null);
        $this->assertNull($grupMenuRoleGrupMenuId);
    }

}
