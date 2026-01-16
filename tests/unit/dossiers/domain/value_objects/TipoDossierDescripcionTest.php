<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierDescripcion;
use Tests\myTest;

class TipoDossierDescripcionTest extends myTest
{
    public function test_create_valid_tipoDossierDescripcion()
    {
        $tipoDossierDescripcion = new TipoDossierDescripcion('test value');
        $this->assertEquals('test value', $tipoDossierDescripcion->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDossierDescripcion(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_tipoDossierDescripcion_value()
    {
        $tipoDossierDescripcion = new TipoDossierDescripcion('test value');
        $this->assertEquals('test value', (string)$tipoDossierDescripcion);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDossierDescripcion = TipoDossierDescripcion::fromNullableString('test value');
        $this->assertInstanceOf(TipoDossierDescripcion::class, $tipoDossierDescripcion);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDossierDescripcion = TipoDossierDescripcion::fromNullableString(null);
        $this->assertNull($tipoDossierDescripcion);
    }

}
