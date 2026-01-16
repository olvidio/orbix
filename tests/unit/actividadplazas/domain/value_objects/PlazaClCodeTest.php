<?php

namespace Tests\unit\actividadplazas\domain\value_objects;

use src\actividadplazas\domain\value_objects\PlazaClCode;
use Tests\myTest;

class PlazaClCodeTest extends myTest
{
    public function test_create_valid_plazaClCode()
    {
        $plazaClCode = new PlazaClCode('test value');
        $this->assertEquals('test value', $plazaClCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PlazaClCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $plazaClCode = PlazaClCode::fromNullableString('test value');
        $this->assertInstanceOf(PlazaClCode::class, $plazaClCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $plazaClCode = PlazaClCode::fromNullableString(null);
        $this->assertNull($plazaClCode);
    }

}
