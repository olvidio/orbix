<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\NombreCentroText;
use Tests\myTest;

class NombreCentroTextTest extends myTest
{
    public function test_create_valid_nombreCentroText()
    {
        $nombreCentroText = new NombreCentroText('test value');
        $this->assertEquals('test value', $nombreCentroText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new NombreCentroText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_nombreCentroText_value()
    {
        $nombreCentroText = new NombreCentroText('test value');
        $this->assertEquals('test value', (string)$nombreCentroText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $nombreCentroText = NombreCentroText::fromNullableString('test value');
        $this->assertInstanceOf(NombreCentroText::class, $nombreCentroText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $nombreCentroText = NombreCentroText::fromNullableString(null);
        $this->assertNull($nombreCentroText);
    }

}
