<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\CongresoName;
use Tests\myTest;

class CongresoNameTest extends myTest
{
    public function test_create_valid_congresoName()
    {
        $congresoName = new CongresoName('test value');
        $this->assertEquals('test value', $congresoName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CongresoName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_congresoName_value()
    {
        $congresoName = new CongresoName('test value');
        $this->assertEquals('test value', (string)$congresoName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $congresoName = CongresoName::fromNullableString('test value');
        $this->assertInstanceOf(CongresoName::class, $congresoName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $congresoName = CongresoName::fromNullableString(null);
        $this->assertNull($congresoName);
    }

}
