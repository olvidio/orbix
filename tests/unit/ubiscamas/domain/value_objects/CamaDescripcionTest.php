<?php

namespace Tests\unit\ubiscamas\domain\value_objects;

use src\ubiscamas\domain\value_objects\CamaDescripcion;
use Tests\myTest;

class CamaDescripcionTest extends myTest
{
    public function test_create_valid_camaDescripcion()
    {
        $desc = new CamaDescripcion('Cama individual');
        $this->assertEquals('Cama individual', $desc->value());
    }

    public function test_to_string_returns_value()
    {
        $desc = new CamaDescripcion('Cama individual');
        $this->assertEquals('Cama individual', (string)$desc);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $desc = CamaDescripcion::fromNullableString('Cama individual');
        $this->assertInstanceOf(CamaDescripcion::class, $desc);
        $this->assertEquals('Cama individual', $desc->value());
    }

    public function test_fromNullableString_returns_null_for_null()
    {
        $this->assertNull(CamaDescripcion::fromNullableString(null));
    }

    public function test_fromNullableString_returns_null_for_empty_string()
    {
        $this->assertNull(CamaDescripcion::fromNullableString(''));
    }
}
