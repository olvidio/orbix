<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\PersonaApellido1Text;
use Tests\myTest;

class PersonaApellido1TextTest extends myTest
{
    public function test_create_valid_personaApellido1Text()
    {
        $personaApellido1Text = new PersonaApellido1Text('test value');
        $this->assertEquals('test value', $personaApellido1Text->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PersonaApellido1Text(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_personaApellido1Text_value()
    {
        $personaApellido1Text = new PersonaApellido1Text('test value');
        $this->assertEquals('test value', (string)$personaApellido1Text);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $personaApellido1Text = PersonaApellido1Text::fromNullableString('test value');
        $this->assertInstanceOf(PersonaApellido1Text::class, $personaApellido1Text);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $personaApellido1Text = PersonaApellido1Text::fromNullableString(null);
        $this->assertNull($personaApellido1Text);
    }

}
