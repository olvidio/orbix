<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EgmTexto;
use Tests\myTest;

class EgmTextoTest extends myTest
{
    public function test_create_valid_egmTexto()
    {
        $egmTexto = new EgmTexto('test value');
        $this->assertEquals('test value', $egmTexto->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EgmTexto(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_egmTexto()
    {
        $egmTexto1 = new EgmTexto('test value');
        $egmTexto2 = new EgmTexto('test value');
        $this->assertTrue($egmTexto1->equals($egmTexto2));
    }

    public function test_equals_returns_false_for_different_egmTexto()
    {
        $egmTexto1 = new EgmTexto('test value');
        $egmTexto2 = new EgmTexto('alternative value');
        $this->assertFalse($egmTexto1->equals($egmTexto2));
    }

    public function test_to_string_returns_egmTexto_value()
    {
        $egmTexto = new EgmTexto('test value');
        $this->assertEquals('test value', (string)$egmTexto);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $egmTexto = EgmTexto::fromNullableString('test value');
        $this->assertInstanceOf(EgmTexto::class, $egmTexto);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $egmTexto = EgmTexto::fromNullableString(null);
        $this->assertNull($egmTexto);
    }

}
