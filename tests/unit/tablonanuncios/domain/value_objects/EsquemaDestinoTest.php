<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use src\tablonanuncios\domain\value_objects\EsquemaDestino;
use Tests\myTest;

class EsquemaDestinoTest extends myTest
{
    public function test_create_valid_esquemaDestino()
    {
        $esquemaDestino = new EsquemaDestino('test value');
        $this->assertEquals('test value', $esquemaDestino->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EsquemaDestino(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_esquemaDestino()
    {
        $esquemaDestino1 = new EsquemaDestino('test value');
        $esquemaDestino2 = new EsquemaDestino('test value');
        $this->assertTrue($esquemaDestino1->equals($esquemaDestino2));
    }

    public function test_equals_returns_false_for_different_esquemaDestino()
    {
        $esquemaDestino1 = new EsquemaDestino('test value');
        $esquemaDestino2 = new EsquemaDestino('alternative value');
        $this->assertFalse($esquemaDestino1->equals($esquemaDestino2));
    }

    public function test_to_string_returns_esquemaDestino_value()
    {
        $esquemaDestino = new EsquemaDestino('test value');
        $this->assertEquals('test value', (string)$esquemaDestino);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $esquemaDestino = EsquemaDestino::fromNullableString('test value');
        $this->assertInstanceOf(EsquemaDestino::class, $esquemaDestino);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $esquemaDestino = EsquemaDestino::fromNullableString(null);
        $this->assertNull($esquemaDestino);
    }

}
