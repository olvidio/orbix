<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierClass;
use Tests\myTest;

class TipoDossierClassTest extends myTest
{
    public function test_create_valid_tipoDossierClass()
    {
        $tipoDossierClass = new TipoDossierClass('test value');
        $this->assertEquals('test value', $tipoDossierClass->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDossierClass(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_tipoDossierClass_value()
    {
        $tipoDossierClass = new TipoDossierClass('test value');
        $this->assertEquals('test value', (string)$tipoDossierClass);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDossierClass = TipoDossierClass::fromNullableString('test value');
        $this->assertInstanceOf(TipoDossierClass::class, $tipoDossierClass);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDossierClass = TipoDossierClass::fromNullableString(null);
        $this->assertNull($tipoDossierClass);
    }

}
