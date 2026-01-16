<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\PersonaTratoCode;
use Tests\myTest;

class PersonaTratoCodeTest extends myTest
{
    //PersonaTratoCode must be at most 5 characters
    public function test_create_valid_personaTratoCode()
    {
        $personaTratoCode = new PersonaTratoCode('test');
        $this->assertEquals('test', $personaTratoCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PersonaTratoCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_personaTratoCode_value()
    {
        $personaTratoCode = new PersonaTratoCode('test');
        $this->assertEquals('test', (string)$personaTratoCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $personaTratoCode = PersonaTratoCode::fromNullableString('test');
        $this->assertInstanceOf(PersonaTratoCode::class, $personaTratoCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $personaTratoCode = PersonaTratoCode::fromNullableString(null);
        $this->assertNull($personaTratoCode);
    }

}
