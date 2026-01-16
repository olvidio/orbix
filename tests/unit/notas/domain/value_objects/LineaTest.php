<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Linea;
use Tests\myTest;

class LineaTest extends myTest
{
    public function test_create_valid_linea()
    {
        $linea = new Linea(123);
        $this->assertEquals(123, $linea->value());
    }

    public function test_to_string_returns_linea_value()
    {
        $linea = new Linea(123);
        $this->assertEquals(123, (string)$linea);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $linea = Linea::fromNullableInt(123);
        $this->assertInstanceOf(Linea::class, $linea);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $linea = Linea::fromNullableInt(null);
        $this->assertNull($linea);
    }

}
