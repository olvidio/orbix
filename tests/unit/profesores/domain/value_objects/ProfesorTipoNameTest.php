<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\ProfesorTipoName;
use Tests\myTest;

class ProfesorTipoNameTest extends myTest
{
    public function test_create_valid_profesorTipoName()
    {
        $profesorTipoName = new ProfesorTipoName('test value');
        $this->assertEquals('test value', $profesorTipoName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProfesorTipoName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_profesorTipoName()
    {
        $profesorTipoName1 = new ProfesorTipoName('test value');
        $profesorTipoName2 = new ProfesorTipoName('test value');
        $this->assertTrue($profesorTipoName1->equals($profesorTipoName2));
    }

    public function test_equals_returns_false_for_different_profesorTipoName()
    {
        $profesorTipoName1 = new ProfesorTipoName('test value');
        $profesorTipoName2 = new ProfesorTipoName('alternative value');
        $this->assertFalse($profesorTipoName1->equals($profesorTipoName2));
    }

    public function test_to_string_returns_profesorTipoName_value()
    {
        $profesorTipoName = new ProfesorTipoName('test value');
        $this->assertEquals('test value', (string)$profesorTipoName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $profesorTipoName = ProfesorTipoName::fromNullableString('test value');
        $this->assertInstanceOf(ProfesorTipoName::class, $profesorTipoName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $profesorTipoName = ProfesorTipoName::fromNullableString(null);
        $this->assertNull($profesorTipoName);
    }

}
