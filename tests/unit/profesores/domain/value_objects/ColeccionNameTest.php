<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\ColeccionName;
use Tests\myTest;

class ColeccionNameTest extends myTest
{
    public function test_create_valid_coleccionName()
    {
        $coleccionName = new ColeccionName('test value');
        $this->assertEquals('test value', $coleccionName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ColeccionName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_coleccionName_value()
    {
        $coleccionName = new ColeccionName('test value');
        $this->assertEquals('test value', (string)$coleccionName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $coleccionName = ColeccionName::fromNullableString('test value');
        $this->assertInstanceOf(ColeccionName::class, $coleccionName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $coleccionName = ColeccionName::fromNullableString(null);
        $this->assertNull($coleccionName);
    }

}
