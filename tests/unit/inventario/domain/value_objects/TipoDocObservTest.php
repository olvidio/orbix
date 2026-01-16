<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\TipoDocObserv;
use Tests\myTest;

class TipoDocObservTest extends myTest
{
    public function test_create_valid_tipoDocObserv()
    {
        $tipoDocObserv = new TipoDocObserv('test value');
        $this->assertEquals('test value', $tipoDocObserv->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDocObserv(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoDocObserv()
    {
        $tipoDocObserv1 = new TipoDocObserv('test value');
        $tipoDocObserv2 = new TipoDocObserv('test value');
        $this->assertTrue($tipoDocObserv1->equals($tipoDocObserv2));
    }

    public function test_equals_returns_false_for_different_tipoDocObserv()
    {
        $tipoDocObserv1 = new TipoDocObserv('test value');
        $tipoDocObserv2 = new TipoDocObserv('alternative value');
        $this->assertFalse($tipoDocObserv1->equals($tipoDocObserv2));
    }

    public function test_to_string_returns_tipoDocObserv_value()
    {
        $tipoDocObserv = new TipoDocObserv('test value');
        $this->assertEquals('test value', (string)$tipoDocObserv);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDocObserv = TipoDocObserv::fromNullableString('test value');
        $this->assertInstanceOf(TipoDocObserv::class, $tipoDocObserv);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDocObserv = TipoDocObserv::fromNullableString(null);
        $this->assertNull($tipoDocObserv);
    }

}
