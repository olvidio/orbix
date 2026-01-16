<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierCampoTo;
use Tests\myTest;

class TipoDossierCampoToTest extends myTest
{
    public function test_create_valid_tipoDossierCampoTo()
    {
        $tipoDossierCampoTo = new TipoDossierCampoTo('testvalue');
        $this->assertEquals('testvalue', $tipoDossierCampoTo->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDossierCampoTo(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_tipoDossierCampoTo_value()
    {
        $tipoDossierCampoTo = new TipoDossierCampoTo('test_value');
        $this->assertEquals('test_value', (string)$tipoDossierCampoTo);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDossierCampoTo = TipoDossierCampoTo::fromNullableString('test_value');
        $this->assertInstanceOf(TipoDossierCampoTo::class, $tipoDossierCampoTo);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDossierCampoTo = TipoDossierCampoTo::fromNullableString(null);
        $this->assertNull($tipoDossierCampoTo);
    }

}
