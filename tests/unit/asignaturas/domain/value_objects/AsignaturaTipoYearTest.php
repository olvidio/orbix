<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaTipoYear;
use Tests\myTest;

class AsignaturaTipoYearTest extends myTest
{
    // Máx 3 caracteres; pensado para dígitos o números romanos (I,V,X)
    public function test_create_valid_asignaturaTipoYear()
    {
        $asignaturaTipoYear = new AsignaturaTipoYear('VI');
        $this->assertEquals('VI', $asignaturaTipoYear->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsignaturaTipoYear(str_repeat('a', 10)); // Assuming max length validation
    }

    public function test_to_string_returns_asignaturaTipoYear_value()
    {
        $asignaturaTipoYear = new AsignaturaTipoYear('VI');
        $this->assertEquals('VI', (string)$asignaturaTipoYear);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asignaturaTipoYear = AsignaturaTipoYear::fromNullableString('VI');
        $this->assertInstanceOf(AsignaturaTipoYear::class, $asignaturaTipoYear);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asignaturaTipoYear = AsignaturaTipoYear::fromNullableString(null);
        $this->assertNull($asignaturaTipoYear);
    }

}
