<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierTablaFrom;
use Tests\myTest;

class TipoDossierTablaFromTest extends myTest
{
    public function test_create_valid_tipoDossierTablaFrom()
    {
        $tipoDossierTablaFrom = new TipoDossierTablaFrom('a');
        $this->assertEquals('a', $tipoDossierTablaFrom->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDossierTablaFrom(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_tipoDossierTablaFrom_value()
    {
        $tipoDossierTablaFrom = new TipoDossierTablaFrom('a');
        $this->assertEquals('a', (string)$tipoDossierTablaFrom);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDossierTablaFrom = TipoDossierTablaFrom::fromNullableString('p');
        $this->assertInstanceOf(TipoDossierTablaFrom::class, $tipoDossierTablaFrom);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDossierTablaFrom = TipoDossierTablaFrom::fromNullableString(null);
        $this->assertNull($tipoDossierTablaFrom);
    }

}
