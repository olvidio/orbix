<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\DepartamentoName;
use Tests\myTest;

class DepartamentoNameTest extends myTest
{
    public function test_create_valid_departamentoName()
    {
        $departamentoName = new DepartamentoName('test value');
        $this->assertEquals('test value', $departamentoName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DepartamentoName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_departamentoName()
    {
        $departamentoName1 = new DepartamentoName('test value');
        $departamentoName2 = new DepartamentoName('test value');
        $this->assertTrue($departamentoName1->equals($departamentoName2));
    }

    public function test_equals_returns_false_for_different_departamentoName()
    {
        $departamentoName1 = new DepartamentoName('test value');
        $departamentoName2 = new DepartamentoName('alternative value');
        $this->assertFalse($departamentoName1->equals($departamentoName2));
    }

    public function test_to_string_returns_departamentoName_value()
    {
        $departamentoName = new DepartamentoName('test value');
        $this->assertEquals('test value', (string)$departamentoName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $departamentoName = DepartamentoName::fromNullableString('test value');
        $this->assertInstanceOf(DepartamentoName::class, $departamentoName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $departamentoName = DepartamentoName::fromNullableString(null);
        $this->assertNull($departamentoName);
    }

}
