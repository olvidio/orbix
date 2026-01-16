<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\MenuParametros;
use Tests\myTest;

class MenuParametrosTest extends myTest
{
    public function test_create_valid_menuParametros()
    {
        $menuParametros = new MenuParametros('test value');
        $this->assertEquals('test value', $menuParametros->value());
    }

    public function test_equals_returns_true_for_same_menuParametros()
    {
        $menuParametros1 = new MenuParametros('test value');
        $menuParametros2 = new MenuParametros('test value');
        $this->assertTrue($menuParametros1->equals($menuParametros2));
    }

    public function test_equals_returns_false_for_different_menuParametros()
    {
        $menuParametros1 = new MenuParametros('test value');
        $menuParametros2 = new MenuParametros('alternative value');
        $this->assertFalse($menuParametros1->equals($menuParametros2));
    }

    public function test_to_string_returns_menuParametros_value()
    {
        $menuParametros = new MenuParametros('test value');
        $this->assertEquals('test value', (string)$menuParametros);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $menuParametros = MenuParametros::fromNullableString('test value');
        $this->assertInstanceOf(MenuParametros::class, $menuParametros);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $menuParametros = MenuParametros::fromNullableString(null);
        $this->assertNull($menuParametros);
    }

}
