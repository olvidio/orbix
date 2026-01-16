<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use src\tablonanuncios\domain\value_objects\EsquemaEmisor;
use Tests\myTest;

class EsquemaEmisorTest extends myTest
{
    public function test_create_valid_esquemaEmisor()
    {
        $esquemaEmisor = new EsquemaEmisor('test value');
        $this->assertEquals('test value', $esquemaEmisor->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EsquemaEmisor(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_esquemaEmisor()
    {
        $esquemaEmisor1 = new EsquemaEmisor('test value');
        $esquemaEmisor2 = new EsquemaEmisor('test value');
        $this->assertTrue($esquemaEmisor1->equals($esquemaEmisor2));
    }

    public function test_equals_returns_false_for_different_esquemaEmisor()
    {
        $esquemaEmisor1 = new EsquemaEmisor('test value');
        $esquemaEmisor2 = new EsquemaEmisor('alternative value');
        $this->assertFalse($esquemaEmisor1->equals($esquemaEmisor2));
    }

    public function test_to_string_returns_esquemaEmisor_value()
    {
        $esquemaEmisor = new EsquemaEmisor('test value');
        $this->assertEquals('test value', (string)$esquemaEmisor);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $esquemaEmisor = EsquemaEmisor::fromNullableString('test value');
        $this->assertInstanceOf(EsquemaEmisor::class, $esquemaEmisor);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $esquemaEmisor = EsquemaEmisor::fromNullableString(null);
        $this->assertNull($esquemaEmisor);
    }

}
