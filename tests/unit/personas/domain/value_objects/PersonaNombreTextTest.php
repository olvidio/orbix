<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\PersonaNombreText;
use Tests\myTest;

class PersonaNombreTextTest extends myTest
{
    public function test_create_valid_personaNombreText()
    {
        $personaNombreText = new PersonaNombreText('test value');
        $this->assertEquals('test value', $personaNombreText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PersonaNombreText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_personaNombreText_value()
    {
        $personaNombreText = new PersonaNombreText('test value');
        $this->assertEquals('test value', (string)$personaNombreText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $personaNombreText = PersonaNombreText::fromNullableString('test value');
        $this->assertInstanceOf(PersonaNombreText::class, $personaNombreText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $personaNombreText = PersonaNombreText::fromNullableString(null);
        $this->assertNull($personaNombreText);
    }

}
