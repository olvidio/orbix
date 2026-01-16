<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Examinador;
use Tests\myTest;

class ExaminadorTest extends myTest
{
    public function test_create_valid_examinador()
    {
        $examinador = new Examinador('test value');
        $this->assertEquals('test value', $examinador->value());
    }

    public function test_to_string_returns_examinador_value()
    {
        $examinador = new Examinador('test value');
        $this->assertEquals('test value', (string)$examinador);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $examinador = Examinador::fromNullableString('test value');
        $this->assertInstanceOf(Examinador::class, $examinador);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $examinador = Examinador::fromNullableString(null);
        $this->assertNull($examinador);
    }

}
