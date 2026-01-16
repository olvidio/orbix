<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\MenuName;
use Tests\myTest;

class MenuNameTest extends myTest
{
    public function test_create_valid_menuName()
    {
        $menuName = new MenuName('test value');
        $this->assertEquals('test value', $menuName->value());
    }

    public function test_equals_returns_true_for_same_menuName()
    {
        $menuName1 = new MenuName('test value');
        $menuName2 = new MenuName('test value');
        $this->assertTrue($menuName1->equals($menuName2));
    }

    public function test_equals_returns_false_for_different_menuName()
    {
        $menuName1 = new MenuName('test value');
        $menuName2 = new MenuName('alternative value');
        $this->assertFalse($menuName1->equals($menuName2));
    }

    public function test_to_string_returns_menuName_value()
    {
        $menuName = new MenuName('test value');
        $this->assertEquals('test value', (string)$menuName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $menuName = MenuName::fromNullableString('test value');
        $this->assertInstanceOf(MenuName::class, $menuName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $menuName = MenuName::fromNullableString(null);
        $this->assertNull($menuName);
    }

}
