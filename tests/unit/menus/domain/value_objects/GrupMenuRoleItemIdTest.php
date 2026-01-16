<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\GrupMenuRoleItemId;
use Tests\myTest;

class GrupMenuRoleItemIdTest extends myTest
{
    public function test_create_valid_grupMenuRoleItemId()
    {
        $grupMenuRoleItemId = new GrupMenuRoleItemId(123);
        $this->assertEquals(123, $grupMenuRoleItemId->value());
    }

    public function test_equals_returns_true_for_same_grupMenuRoleItemId()
    {
        $grupMenuRoleItemId1 = new GrupMenuRoleItemId(123);
        $grupMenuRoleItemId2 = new GrupMenuRoleItemId(123);
        $this->assertTrue($grupMenuRoleItemId1->equals($grupMenuRoleItemId2));
    }

    public function test_equals_returns_false_for_different_grupMenuRoleItemId()
    {
        $grupMenuRoleItemId1 = new GrupMenuRoleItemId(123);
        $grupMenuRoleItemId2 = new GrupMenuRoleItemId(456);
        $this->assertFalse($grupMenuRoleItemId1->equals($grupMenuRoleItemId2));
    }

    public function test_to_string_returns_grupMenuRoleItemId_value()
    {
        $grupMenuRoleItemId = new GrupMenuRoleItemId(123);
        $this->assertEquals(123, (string)$grupMenuRoleItemId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $grupMenuRoleItemId = GrupMenuRoleItemId::fromNullableInt(123);
        $this->assertInstanceOf(GrupMenuRoleItemId::class, $grupMenuRoleItemId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $grupMenuRoleItemId = GrupMenuRoleItemId::fromNullableInt(null);
        $this->assertNull($grupMenuRoleItemId);
    }

}
