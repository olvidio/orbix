<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\ConfigParametroCode;
use Tests\myTest;

class ConfigParametroCodeTest extends myTest
{
    public function test_create_valid_configParametroCode()
    {
        $configParametroCode = new ConfigParametroCode('test value');
        $this->assertEquals('test value', $configParametroCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ConfigParametroCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_configParametroCode()
    {
        $configParametroCode1 = new ConfigParametroCode('test value');
        $configParametroCode2 = new ConfigParametroCode('test value');
        $this->assertTrue($configParametroCode1->equals($configParametroCode2));
    }

    public function test_equals_returns_false_for_different_configParametroCode()
    {
        $configParametroCode1 = new ConfigParametroCode('test value');
        $configParametroCode2 = new ConfigParametroCode('alternative value');
        $this->assertFalse($configParametroCode1->equals($configParametroCode2));
    }

    public function test_to_string_returns_configParametroCode_value()
    {
        $configParametroCode = new ConfigParametroCode('test value');
        $this->assertEquals('test value', (string)$configParametroCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $configParametroCode = ConfigParametroCode::fromNullableString('test value');
        $this->assertInstanceOf(ConfigParametroCode::class, $configParametroCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $configParametroCode = ConfigParametroCode::fromNullableString(null);
        $this->assertNull($configParametroCode);
    }

}
