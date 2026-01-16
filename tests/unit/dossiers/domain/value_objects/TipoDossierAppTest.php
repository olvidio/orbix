<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierApp;
use Tests\myTest;

class TipoDossierAppTest extends myTest
{
    public function test_create_valid_tipoDossierApp()
    {
        $tipoDossierApp = new TipoDossierApp('test value');
        $this->assertEquals('test value', $tipoDossierApp->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDossierApp(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_tipoDossierApp_value()
    {
        $tipoDossierApp = new TipoDossierApp('test value');
        $this->assertEquals('test value', (string)$tipoDossierApp);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDossierApp = TipoDossierApp::fromNullableString('test value');
        $this->assertInstanceOf(TipoDossierApp::class, $tipoDossierApp);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDossierApp = TipoDossierApp::fromNullableString(null);
        $this->assertNull($tipoDossierApp);
    }

}
