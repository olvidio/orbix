<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\ActaNumero;
use Tests\myTest;

class ActaNumeroTest extends myTest
{
    public function test_create_valid_actaNumero()
    {
        $actaNumero = new ActaNumero('test value');
        $this->assertEquals('test value', $actaNumero->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ActaNumero(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_actaNumero_value()
    {
        $actaNumero = new ActaNumero('test value');
        $this->assertEquals('test value', (string)$actaNumero);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $actaNumero = ActaNumero::fromNullableString('test value');
        $this->assertInstanceOf(ActaNumero::class, $actaNumero);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $actaNumero = ActaNumero::fromNullableString(null);
        $this->assertNull($actaNumero);
    }

}
