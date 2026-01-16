<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\ModuloName;
use Tests\myTest;

class ModuloNameTest extends myTest
{
    public function test_create_valid_moduloName()
    {
        $moduloName = new ModuloName('test value');
        $this->assertEquals('test value', $moduloName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ModuloName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_moduloName()
    {
        $moduloName1 = new ModuloName('test value');
        $moduloName2 = new ModuloName('test value');
        $this->assertTrue($moduloName1->equals($moduloName2));
    }

    public function test_equals_returns_false_for_different_moduloName()
    {
        $moduloName1 = new ModuloName('test value');
        $moduloName2 = new ModuloName('alternative value');
        $this->assertFalse($moduloName1->equals($moduloName2));
    }

    public function test_to_string_returns_moduloName_value()
    {
        $moduloName = new ModuloName('test value');
        $this->assertEquals('test value', (string)$moduloName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $moduloName = ModuloName::fromNullableString('test value');
        $this->assertInstanceOf(ModuloName::class, $moduloName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $moduloName = ModuloName::fromNullableString(null);
        $this->assertNull($moduloName);
    }

}
