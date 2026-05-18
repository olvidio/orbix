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

    public function test_acepta_barra_y_punto_medio(): void
    {
        $text = new PersonaApellido2Text('Richi/Ricardo');
        $this->assertSame('Richi/Ricardo', $text->value());

        $conPunto = new PersonaApellido2Text('Muñoz·García');
        $this->assertSame('Muñoz·García', $conPunto->value());
    }

    public function test_caracter_no_permitido_incluye_detalle_en_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/PersonaApellido2Text.*no permitidos:/');
        new PersonaApellido2Text('García & López');
    }

}
