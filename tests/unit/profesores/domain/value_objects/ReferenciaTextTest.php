<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\ReferenciaText;
use Tests\myTest;

class ReferenciaTextTest extends myTest
{
    public function test_create_valid_referenciaText()
    {
        $referenciaText = new ReferenciaText('test value');
        $this->assertEquals('test value', $referenciaText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ReferenciaText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_referenciaText_value()
    {
        $referenciaText = new ReferenciaText('test value');
        $this->assertEquals('test value', (string)$referenciaText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $referenciaText = ReferenciaText::fromNullableString('test value');
        $this->assertInstanceOf(ReferenciaText::class, $referenciaText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $referenciaText = ReferenciaText::fromNullableString(null);
        $this->assertNull($referenciaText);
    }

}
