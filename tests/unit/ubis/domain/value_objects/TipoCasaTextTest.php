<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TipoCasaText;
use Tests\myTest;

class TipoCasaTextTest extends myTest
{
    public function test_create_valid_tipoCasaText()
    {
        $tipoCasaText = new TipoCasaText('test value');
        $this->assertEquals('test value', $tipoCasaText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoCasaText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoCasaText()
    {
        $tipoCasaText1 = new TipoCasaText('test value');
        $tipoCasaText2 = new TipoCasaText('test value');
        $this->assertTrue($tipoCasaText1->equals($tipoCasaText2));
    }

    public function test_equals_returns_false_for_different_tipoCasaText()
    {
        $tipoCasaText1 = new TipoCasaText('test value');
        $tipoCasaText2 = new TipoCasaText('alternative value');
        $this->assertFalse($tipoCasaText1->equals($tipoCasaText2));
    }

    public function test_to_string_returns_tipoCasaText_value()
    {
        $tipoCasaText = new TipoCasaText('test value');
        $this->assertEquals('test value', (string)$tipoCasaText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoCasaText = TipoCasaText::fromNullableString('test value');
        $this->assertInstanceOf(TipoCasaText::class, $tipoCasaText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoCasaText = TipoCasaText::fromNullableString(null);
        $this->assertNull($tipoCasaText);
    }

}
