<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\PersonaApellido2Text;
use Tests\myTest;

class PersonaApellido2TextTest extends myTest
{
    public function test_create_valid_personaApellido2Text()
    {
        $personaApellido2Text = new PersonaApellido2Text('test value');
        $this->assertEquals('test value', $personaApellido2Text->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PersonaApellido2Text(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_personaApellido2Text_value()
    {
        $personaApellido2Text = new PersonaApellido2Text('test value');
        $this->assertEquals('test value', (string)$personaApellido2Text);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $personaApellido2Text = PersonaApellido2Text::fromNullableString('test value');
        $this->assertInstanceOf(PersonaApellido2Text::class, $personaApellido2Text);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $personaApellido2Text = PersonaApellido2Text::fromNullableString(null);
        $this->assertNull($personaApellido2Text);
    }

}
