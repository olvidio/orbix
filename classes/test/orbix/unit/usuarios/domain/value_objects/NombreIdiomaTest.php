<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\NombreIdioma;
use Tests\myTest;

class NombreIdiomaTest extends myTest
{
    public function test_create_valid_nombre_idioma()
    {
        $nombreIdioma = new NombreIdioma('English');
        $this->assertEquals('English', $nombreIdioma->value());
    }

    public function test_empty_nombre_idioma_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Language name cannot be empty');
        new NombreIdioma('');
    }

    public function test_equals_returns_true_for_same_nombre_idioma()
    {
        $nombreIdioma1 = new NombreIdioma('English');
        $nombreIdioma2 = new NombreIdioma('English');
        $this->assertTrue($nombreIdioma1->equals($nombreIdioma2));
    }

    public function test_equals_returns_false_for_different_nombre_idioma()
    {
        $nombreIdioma1 = new NombreIdioma('English');
        $nombreIdioma2 = new NombreIdioma('Spanish');
        $this->assertFalse($nombreIdioma1->equals($nombreIdioma2));
    }

    public function test_to_string_returns_nombre_idioma_value()
    {
        $nombreIdioma = new NombreIdioma('English');
        $this->assertEquals('English', (string)$nombreIdioma);
    }
}