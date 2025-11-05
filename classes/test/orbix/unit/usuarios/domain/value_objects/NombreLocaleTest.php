<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\NombreLocale;
use Tests\myTest;

class NombreLocaleTest extends myTest
{
    public function test_create_valid_nombre_locale()
    {
        $nombreLocale = new NombreLocale('English (United States)');
        $this->assertEquals('English (United States)', $nombreLocale->value());
    }

    public function test_empty_nombre_locale_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Locale name cannot be empty');
        new NombreLocale('');
    }

    public function test_equals_returns_true_for_same_nombre_locale()
    {
        $nombreLocale1 = new NombreLocale('English (United States)');
        $nombreLocale2 = new NombreLocale('English (United States)');
        $this->assertTrue($nombreLocale1->equals($nombreLocale2));
    }

    public function test_equals_returns_false_for_different_nombre_locale()
    {
        $nombreLocale1 = new NombreLocale('English (United States)');
        $nombreLocale2 = new NombreLocale('Spanish (Spain)');
        $this->assertFalse($nombreLocale1->equals($nombreLocale2));
    }

    public function test_to_string_returns_nombre_locale_value()
    {
        $nombreLocale = new NombreLocale('English (United States)');
        $this->assertEquals('English (United States)', (string)$nombreLocale);
    }
}