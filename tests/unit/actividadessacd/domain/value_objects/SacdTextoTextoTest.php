<?php

namespace Tests\unit\actividadessacd\domain\value_objects;

use src\actividadessacd\domain\value_objects\SacdTextoTexto;
use Tests\myTest;

class SacdTextoTextoTest extends myTest
{
    public function test_create_valid_sacdTextoTexto()
    {
        $sacdTextoTexto = new SacdTextoTexto('test value');
        $this->assertEquals('test value', $sacdTextoTexto->value());
    }

    public function test_equals_returns_true_for_same_sacdTextoTexto()
    {
        $sacdTextoTexto1 = new SacdTextoTexto('test value');
        $sacdTextoTexto2 = new SacdTextoTexto('test value');
        $this->assertTrue($sacdTextoTexto1->equals($sacdTextoTexto2));
    }

    public function test_equals_returns_false_for_different_sacdTextoTexto()
    {
        $sacdTextoTexto1 = new SacdTextoTexto('test value');
        $sacdTextoTexto2 = new SacdTextoTexto('alternative value');
        $this->assertFalse($sacdTextoTexto1->equals($sacdTextoTexto2));
    }

    public function test_to_string_returns_sacdTextoTexto_value()
    {
        $sacdTextoTexto = new SacdTextoTexto('test value');
        $this->assertEquals('test value', (string)$sacdTextoTexto);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $sacdTextoTexto = SacdTextoTexto::fromNullableString('test value');
        $this->assertInstanceOf(SacdTextoTexto::class, $sacdTextoTexto);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $sacdTextoTexto = SacdTextoTexto::fromNullableString(null);
        $this->assertNull($sacdTextoTexto);
    }

}
