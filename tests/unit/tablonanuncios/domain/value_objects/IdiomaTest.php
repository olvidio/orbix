<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use src\tablonanuncios\domain\value_objects\Idioma;
use Tests\myTest;

class IdiomaTest extends myTest
{
    public function test_create_valid_idioma()
    {
        $idioma = new Idioma('es_ES.UTF-8');
        $this->assertEquals('es_ES.UTF-8', $idioma->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Idioma(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_idioma()
    {
        $idioma1 = new Idioma('es_ES.UTF-8');
        $idioma2 = new Idioma('es_ES.UTF-8');
        $this->assertTrue($idioma1->equals($idioma2));
    }

    public function test_equals_returns_false_for_different_idioma()
    {
        $idioma1 = new Idioma('es_ES.UTF-8');
        $idioma2 = new Idioma('en_US.UTF-8');
        $this->assertFalse($idioma1->equals($idioma2));
    }

    public function test_to_string_returns_idioma_value()
    {
        $idioma = new Idioma('es_ES.UTF-8');
        $this->assertEquals('es_ES.UTF-8', (string)$idioma);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $idioma = Idioma::fromNullableString('es_ES.UTF-8');
        $this->assertInstanceOf(Idioma::class, $idioma);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $idioma = Idioma::fromNullableString(null);
        $this->assertNull($idioma);
    }

}
