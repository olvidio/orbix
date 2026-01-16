<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\EscritoCese;
use Tests\myTest;

class EscritoCeseTest extends myTest
{
    public function test_create_valid_escritoCese()
    {
        $escritoCese = new EscritoCese('test value');
        $this->assertEquals('test value', $escritoCese->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EscritoCese(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_escritoCese_value()
    {
        $escritoCese = new EscritoCese('test value');
        $this->assertEquals('test value', (string)$escritoCese);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $escritoCese = EscritoCese::fromNullableString('test value');
        $this->assertInstanceOf(EscritoCese::class, $escritoCese);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $escritoCese = EscritoCese::fromNullableString(null);
        $this->assertNull($escritoCese);
    }

}
