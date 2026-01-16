<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\AppName;
use Tests\myTest;

class AppNameTest extends myTest
{
    public function test_create_valid_appName()
    {
        $appName = new AppName('test value');
        $this->assertEquals('test value', $appName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AppName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_appName()
    {
        $appName1 = new AppName('test value');
        $appName2 = new AppName('test value');
        $this->assertTrue($appName1->equals($appName2));
    }

    public function test_equals_returns_false_for_different_appName()
    {
        $appName1 = new AppName('test value');
        $appName2 = new AppName('alternative value');
        $this->assertFalse($appName1->equals($appName2));
    }

    public function test_to_string_returns_appName_value()
    {
        $appName = new AppName('test value');
        $this->assertEquals('test value', (string)$appName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $appName = AppName::fromNullableString('test value');
        $this->assertInstanceOf(AppName::class, $appName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $appName = AppName::fromNullableString(null);
        $this->assertNull($appName);
    }

}
