<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Breve;
use Tests\myTest;

class BreveTest extends myTest
{
    public function test_create_valid_breve()
    {
        $breve = new Breve('test value');
        $this->assertEquals('test value', $breve->value());
    }

    public function test_to_string_returns_breve_value()
    {
        $breve = new Breve('test value');
        $this->assertEquals('test value', (string)$breve);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $breve = Breve::fromNullableString('test value');
        $this->assertInstanceOf(Breve::class, $breve);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $breve = Breve::fromNullableString(null);
        $this->assertNull($breve);
    }

}
