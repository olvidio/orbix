<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\BibliotecaText;
use Tests\myTest;

class BibliotecaTextTest extends myTest
{
    public function test_create_valid_bibliotecaText()
    {
        $bibliotecaText = new BibliotecaText('test value');
        $this->assertEquals('test value', $bibliotecaText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new BibliotecaText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_bibliotecaText()
    {
        $bibliotecaText1 = new BibliotecaText('test value');
        $bibliotecaText2 = new BibliotecaText('test value');
        $this->assertTrue($bibliotecaText1->equals($bibliotecaText2));
    }

    public function test_equals_returns_false_for_different_bibliotecaText()
    {
        $bibliotecaText1 = new BibliotecaText('test value');
        $bibliotecaText2 = new BibliotecaText('alternative value');
        $this->assertFalse($bibliotecaText1->equals($bibliotecaText2));
    }

    public function test_to_string_returns_bibliotecaText_value()
    {
        $bibliotecaText = new BibliotecaText('test value');
        $this->assertEquals('test value', (string)$bibliotecaText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $bibliotecaText = BibliotecaText::fromNullableString('test value');
        $this->assertInstanceOf(BibliotecaText::class, $bibliotecaText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $bibliotecaText = BibliotecaText::fromNullableString(null);
        $this->assertNull($bibliotecaText);
    }

}
