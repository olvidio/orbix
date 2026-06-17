<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\EscritoNombramiento;
use Tests\myTest;

class EscritoNombramientoTest extends myTest
{
    public function test_create_valid_escritoNombramiento()
    {
        $escritoNombramiento = new EscritoNombramiento('test value');
        $this->assertEquals('test value', $escritoNombramiento->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $long = str_repeat('a', 1000);
        try {
            new EscritoNombramiento($long);
            $this->fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('length=1000', $e->getMessage());
            $this->assertStringContainsString('value=', $e->getMessage());
        }
    }

    public function test_to_string_returns_escritoNombramiento_value()
    {
        $escritoNombramiento = new EscritoNombramiento('test value');
        $this->assertEquals('test value', (string)$escritoNombramiento);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $escritoNombramiento = EscritoNombramiento::fromNullableString('test value');
        $this->assertInstanceOf(EscritoNombramiento::class, $escritoNombramiento);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $escritoNombramiento = EscritoNombramiento::fromNullableString(null);
        $this->assertNull($escritoNombramiento);
    }

}
