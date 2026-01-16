<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\GrupMenuName;
use Tests\myTest;

class GrupMenuNameTest extends myTest
{
    public function test_create_valid_grupMenuName()
    {
        $grupMenuName = new GrupMenuName('test value');
        $this->assertEquals('test value', $grupMenuName->value());
    }

    public function test_equals_returns_true_for_same_grupMenuName()
    {
        $grupMenuName1 = new GrupMenuName('test value');
        $grupMenuName2 = new GrupMenuName('test value');
        $this->assertTrue($grupMenuName1->equals($grupMenuName2));
    }

    public function test_equals_returns_false_for_different_grupMenuName()
    {
        $grupMenuName1 = new GrupMenuName('test value');
        $grupMenuName2 = new GrupMenuName('alternative value');
        $this->assertFalse($grupMenuName1->equals($grupMenuName2));
    }

    public function test_to_string_returns_grupMenuName_value()
    {
        $grupMenuName = new GrupMenuName('test value');
        $this->assertEquals('test value', (string)$grupMenuName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $grupMenuName = GrupMenuName::fromNullableString('test value');
        $this->assertInstanceOf(GrupMenuName::class, $grupMenuName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $grupMenuName = GrupMenuName::fromNullableString(null);
        $this->assertNull($grupMenuName);
    }

}
