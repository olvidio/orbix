<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\TipoDocSigla;
use Tests\myTest;

class TipoDocSiglaTest extends myTest
{
    public function test_create_valid_tipoDocSigla()
    {
        $tipoDocSigla = new TipoDocSigla('test value');
        $this->assertEquals('test value', $tipoDocSigla->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDocSigla(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoDocSigla()
    {
        $tipoDocSigla1 = new TipoDocSigla('test value');
        $tipoDocSigla2 = new TipoDocSigla('test value');
        $this->assertTrue($tipoDocSigla1->equals($tipoDocSigla2));
    }

    public function test_equals_returns_false_for_different_tipoDocSigla()
    {
        $tipoDocSigla1 = new TipoDocSigla('test value');
        $tipoDocSigla2 = new TipoDocSigla('alternative value');
        $this->assertFalse($tipoDocSigla1->equals($tipoDocSigla2));
    }

    public function test_to_string_returns_tipoDocSigla_value()
    {
        $tipoDocSigla = new TipoDocSigla('test value');
        $this->assertEquals('test value', (string)$tipoDocSigla);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDocSigla = TipoDocSigla::fromNullableString('test value');
        $this->assertInstanceOf(TipoDocSigla::class, $tipoDocSigla);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDocSigla = TipoDocSigla::fromNullableString(null);
        $this->assertNull($tipoDocSigla);
    }

}
