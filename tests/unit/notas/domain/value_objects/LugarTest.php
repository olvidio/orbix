<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Lugar;
use Tests\myTest;

class LugarTest extends myTest
{
    public function test_create_valid_lugar()
    {
        $lugar = new Lugar('test value');
        $this->assertEquals('test value', $lugar->value());
    }

    public function test_to_string_returns_lugar_value()
    {
        $lugar = new Lugar('test value');
        $this->assertEquals('test value', (string)$lugar);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $lugar = Lugar::fromNullableString('test value');
        $this->assertInstanceOf(Lugar::class, $lugar);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $lugar = Lugar::fromNullableString(null);
        $this->assertNull($lugar);
    }

}
