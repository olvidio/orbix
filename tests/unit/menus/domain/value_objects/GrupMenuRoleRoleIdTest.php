<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\GrupMenuRoleRoleId;
use Tests\myTest;

class GrupMenuRoleRoleIdTest extends myTest
{
    public function test_create_valid_grupMenuRoleRoleId()
    {
        $grupMenuRoleRoleId = new GrupMenuRoleRoleId(123);
        $this->assertEquals(123, $grupMenuRoleRoleId->value());
    }

    public function test_equals_returns_true_for_same_grupMenuRoleRoleId()
    {
        $grupMenuRoleRoleId1 = new GrupMenuRoleRoleId(123);
        $grupMenuRoleRoleId2 = new GrupMenuRoleRoleId(123);
        $this->assertTrue($grupMenuRoleRoleId1->equals($grupMenuRoleRoleId2));
    }

    public function test_equals_returns_false_for_different_grupMenuRoleRoleId()
    {
        $grupMenuRoleRoleId1 = new GrupMenuRoleRoleId(123);
        $grupMenuRoleRoleId2 = new GrupMenuRoleRoleId(456);
        $this->assertFalse($grupMenuRoleRoleId1->equals($grupMenuRoleRoleId2));
    }

    public function test_to_string_returns_grupMenuRoleRoleId_value()
    {
        $grupMenuRoleRoleId = new GrupMenuRoleRoleId(123);
        $this->assertEquals(123, (string)$grupMenuRoleRoleId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $grupMenuRoleRoleId = GrupMenuRoleRoleId::fromNullableInt(123);
        $this->assertInstanceOf(GrupMenuRoleRoleId::class, $grupMenuRoleRoleId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $grupMenuRoleRoleId = GrupMenuRoleRoleId::fromNullableInt(null);
        $this->assertNull($grupMenuRoleRoleId);
    }

}
