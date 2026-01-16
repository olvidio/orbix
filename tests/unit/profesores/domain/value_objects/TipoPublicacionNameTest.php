<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\TipoPublicacionName;
use Tests\myTest;

class TipoPublicacionNameTest extends myTest
{
    public function test_create_valid_tipoPublicacionName()
    {
        $tipoPublicacionName = new TipoPublicacionName('test value');
        $this->assertEquals('test value', $tipoPublicacionName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoPublicacionName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_tipoPublicacionName_value()
    {
        $tipoPublicacionName = new TipoPublicacionName('test value');
        $this->assertEquals('test value', (string)$tipoPublicacionName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoPublicacionName = TipoPublicacionName::fromNullableString('test value');
        $this->assertInstanceOf(TipoPublicacionName::class, $tipoPublicacionName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoPublicacionName = TipoPublicacionName::fromNullableString(null);
        $this->assertNull($tipoPublicacionName);
    }

}
