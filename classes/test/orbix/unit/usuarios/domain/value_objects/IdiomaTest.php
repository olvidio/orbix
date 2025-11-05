<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\Idioma;
use Tests\myTest;

class IdiomaTest extends myTest
{
    public function test_create_valid_idioma()
    {
        $idioma = new Idioma('en');
        $this->assertEquals('en', $idioma->value());
    }

    public function test_empty_idioma_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Language cannot be empty');
        new Idioma('');
    }

    public function test_equals_returns_true_for_same_idioma()
    {
        $idioma1 = new Idioma('en');
        $idioma2 = new Idioma('en');
        $this->assertTrue($idioma1->equals($idioma2));
    }

    public function test_equals_returns_false_for_different_idioma()
    {
        $idioma1 = new Idioma('en');
        $idioma2 = new Idioma('es');
        $this->assertFalse($idioma1->equals($idioma2));
    }

    public function test_to_string_returns_idioma_value()
    {
        $idioma = new Idioma('en');
        $this->assertEquals('en', (string)$idioma);
    }
}