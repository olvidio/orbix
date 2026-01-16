<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\TemplateMenuName;
use Tests\myTest;

class TemplateMenuNameTest extends myTest
{
    public function test_create_valid_templateMenuName()
    {
        $templateMenuName = new TemplateMenuName('test value');
        $this->assertEquals('test value', $templateMenuName->value());
    }

    public function test_equals_returns_true_for_same_templateMenuName()
    {
        $templateMenuName1 = new TemplateMenuName('test value');
        $templateMenuName2 = new TemplateMenuName('test value');
        $this->assertTrue($templateMenuName1->equals($templateMenuName2));
    }

    public function test_equals_returns_false_for_different_templateMenuName()
    {
        $templateMenuName1 = new TemplateMenuName('test value');
        $templateMenuName2 = new TemplateMenuName('alternative value');
        $this->assertFalse($templateMenuName1->equals($templateMenuName2));
    }

    public function test_to_string_returns_templateMenuName_value()
    {
        $templateMenuName = new TemplateMenuName('test value');
        $this->assertEquals('test value', (string)$templateMenuName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $templateMenuName = TemplateMenuName::fromNullableString('test value');
        $this->assertInstanceOf(TemplateMenuName::class, $templateMenuName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $templateMenuName = TemplateMenuName::fromNullableString(null);
        $this->assertNull($templateMenuName);
    }

}
