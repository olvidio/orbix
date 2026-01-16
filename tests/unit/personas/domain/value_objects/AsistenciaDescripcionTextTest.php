<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\AsistenciaDescripcionText;
use Tests\myTest;

class AsistenciaDescripcionTextTest extends myTest
{
    public function test_create_valid_asistenciaDescripcionText()
    {
        $asistenciaDescripcionText = new AsistenciaDescripcionText('test value');
        $this->assertEquals('test value', $asistenciaDescripcionText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsistenciaDescripcionText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_asistenciaDescripcionText_value()
    {
        $asistenciaDescripcionText = new AsistenciaDescripcionText('test value');
        $this->assertEquals('test value', (string)$asistenciaDescripcionText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asistenciaDescripcionText = AsistenciaDescripcionText::fromNullableString('test value');
        $this->assertInstanceOf(AsistenciaDescripcionText::class, $asistenciaDescripcionText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asistenciaDescripcionText = AsistenciaDescripcionText::fromNullableString(null);
        $this->assertNull($asistenciaDescripcionText);
    }

}
