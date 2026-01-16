<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\ProfesionText;
use Tests\myTest;

class ProfesionTextTest extends myTest
{
    public function test_create_valid_profesionText()
    {
        $profesionText = new ProfesionText('test value');
        $this->assertEquals('test value', $profesionText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProfesionText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_profesionText_value()
    {
        $profesionText = new ProfesionText('test value');
        $this->assertEquals('test value', (string)$profesionText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $profesionText = ProfesionText::fromNullableString('test value');
        $this->assertInstanceOf(ProfesionText::class, $profesionText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $profesionText = ProfesionText::fromNullableString(null);
        $this->assertNull($profesionText);
    }

}
