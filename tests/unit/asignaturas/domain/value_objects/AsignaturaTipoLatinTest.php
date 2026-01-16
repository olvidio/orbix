<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaTipoLatin;
use Tests\myTest;

class AsignaturaTipoLatinTest extends myTest
{
    public function test_create_valid_asignaturaTipoLatin()
    {
        $asignaturaTipoLatin = new AsignaturaTipoLatin('test value');
        $this->assertEquals('test value', $asignaturaTipoLatin->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsignaturaTipoLatin(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_asignaturaTipoLatin_value()
    {
        $asignaturaTipoLatin = new AsignaturaTipoLatin('test value');
        $this->assertEquals('test value', (string)$asignaturaTipoLatin);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asignaturaTipoLatin = AsignaturaTipoLatin::fromNullableString('test value');
        $this->assertInstanceOf(AsignaturaTipoLatin::class, $asignaturaTipoLatin);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asignaturaTipoLatin = AsignaturaTipoLatin::fromNullableString(null);
        $this->assertNull($asignaturaTipoLatin);
    }

}
