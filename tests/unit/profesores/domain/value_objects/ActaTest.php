<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\Acta;
use Tests\myTest;

class ActaTest extends myTest
{
    public function test_create_valid_acta()
    {
        $acta = new Acta('test value');
        $this->assertEquals('test value', $acta->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Acta(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_acta_value()
    {
        $acta = new Acta('test value');
        $this->assertEquals('test value', (string)$acta);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $acta = Acta::fromNullableString('test value');
        $this->assertInstanceOf(Acta::class, $acta);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $acta = Acta::fromNullableString(null);
        $this->assertNull($acta);
    }

}
