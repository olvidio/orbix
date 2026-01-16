<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\ModuloDescription;
use Tests\myTest;

class ModuloDescriptionTest extends myTest
{
    public function test_create_valid_moduloDescription()
    {
        $moduloDescription = new ModuloDescription('test value');
        $this->assertEquals('test value', $moduloDescription->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ModuloDescription(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_moduloDescription()
    {
        $moduloDescription1 = new ModuloDescription('test value');
        $moduloDescription2 = new ModuloDescription('test value');
        $this->assertTrue($moduloDescription1->equals($moduloDescription2));
    }

    public function test_equals_returns_false_for_different_moduloDescription()
    {
        $moduloDescription1 = new ModuloDescription('test value');
        $moduloDescription2 = new ModuloDescription('alternative value');
        $this->assertFalse($moduloDescription1->equals($moduloDescription2));
    }

    public function test_to_string_returns_moduloDescription_value()
    {
        $moduloDescription = new ModuloDescription('test value');
        $this->assertEquals('test value', (string)$moduloDescription);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $moduloDescription = ModuloDescription::fromNullableString('test value');
        $this->assertInstanceOf(ModuloDescription::class, $moduloDescription);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $moduloDescription = ModuloDescription::fromNullableString(null);
        $this->assertNull($moduloDescription);
    }

}
