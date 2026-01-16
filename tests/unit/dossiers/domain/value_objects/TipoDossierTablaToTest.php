<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierTablaTo;
use Tests\myTest;

class TipoDossierTablaToTest extends myTest
{
    public function test_create_valid_tipoDossierTablaTo()
    {
        $tipoDossierTablaTo = new TipoDossierTablaTo('test_value');
        $this->assertEquals('test_value', $tipoDossierTablaTo->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDossierTablaTo(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_tipoDossierTablaTo_value()
    {
        $tipoDossierTablaTo = new TipoDossierTablaTo('test_value');
        $this->assertEquals('test_value', (string)$tipoDossierTablaTo);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDossierTablaTo = TipoDossierTablaTo::fromNullableString('test_value');
        $this->assertInstanceOf(TipoDossierTablaTo::class, $tipoDossierTablaTo);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDossierTablaTo = TipoDossierTablaTo::fromNullableString(null);
        $this->assertNull($tipoDossierTablaTo);
    }

}
