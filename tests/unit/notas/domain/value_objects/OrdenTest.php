<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Orden;
use Tests\myTest;

class OrdenTest extends myTest
{
    public function test_create_valid_orden()
    {
        $orden = new Orden(123);
        $this->assertEquals(123, $orden->value());
    }

    public function test_to_string_returns_orden_value()
    {
        $orden = new Orden(123);
        $this->assertEquals(123, (string)$orden);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $orden = Orden::fromNullableInt(123);
        $this->assertInstanceOf(Orden::class, $orden);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $orden = Orden::fromNullableInt(null);
        $this->assertNull($orden);
    }

}
