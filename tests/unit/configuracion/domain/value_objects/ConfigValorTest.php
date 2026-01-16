<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\ConfigValor;
use Tests\myTest;

class ConfigValorTest extends myTest
{
    public function test_create_valid_configValor()
    {
        $configValor = new ConfigValor('test value');
        $this->assertEquals('test value', $configValor->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ConfigValor(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_configValor()
    {
        $configValor1 = new ConfigValor('test value');
        $configValor2 = new ConfigValor('test value');
        $this->assertTrue($configValor1->equals($configValor2));
    }

    public function test_equals_returns_false_for_different_configValor()
    {
        $configValor1 = new ConfigValor('test value');
        $configValor2 = new ConfigValor('alternative value');
        $this->assertFalse($configValor1->equals($configValor2));
    }

    public function test_to_string_returns_configValor_value()
    {
        $configValor = new ConfigValor('test value');
        $this->assertEquals('test value', (string)$configValor);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $configValor = ConfigValor::fromNullableString('test value');
        $this->assertInstanceOf(ConfigValor::class, $configValor);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $configValor = ConfigValor::fromNullableString(null);
        $this->assertNull($configValor);
    }

}
