<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\PersonaTablaCode;
use Tests\myTest;

class PersonaTablaCodeTest extends myTest
{
    // PersonaTablaCode must be at most 6 characters
    public function test_create_valid_personaTablaCode()
    {
        $personaTablaCode = new PersonaTablaCode('test');
        $this->assertEquals('test', $personaTablaCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PersonaTablaCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_personaTablaCode_value()
    {
        $personaTablaCode = new PersonaTablaCode('test');
        $this->assertEquals('test', (string)$personaTablaCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $personaTablaCode = PersonaTablaCode::fromNullableString('test');
        $this->assertInstanceOf(PersonaTablaCode::class, $personaTablaCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $personaTablaCode = PersonaTablaCode::fromNullableString(null);
        $this->assertNull($personaTablaCode);
    }

}
