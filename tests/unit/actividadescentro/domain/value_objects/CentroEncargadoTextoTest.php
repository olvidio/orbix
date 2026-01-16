<?php

namespace Tests\unit\actividadescentro\domain\value_objects;

use src\actividadescentro\domain\value_objects\CentroEncargadoTexto;
use Tests\myTest;

class CentroEncargadoTextoTest extends myTest
{
    public function test_create_valid_centroEncargadoTexto()
    {
        $centroEncargadoTexto = new CentroEncargadoTexto('test value');
        $this->assertEquals('test value', $centroEncargadoTexto->value());
    }

    public function test_equals_returns_true_for_same_centroEncargadoTexto()
    {
        $centroEncargadoTexto1 = new CentroEncargadoTexto('test value');
        $centroEncargadoTexto2 = new CentroEncargadoTexto('test value');
        $this->assertTrue($centroEncargadoTexto1->equals($centroEncargadoTexto2));
    }

    public function test_equals_returns_false_for_different_centroEncargadoTexto()
    {
        $centroEncargadoTexto1 = new CentroEncargadoTexto('test value');
        $centroEncargadoTexto2 = new CentroEncargadoTexto('alternative value');
        $this->assertFalse($centroEncargadoTexto1->equals($centroEncargadoTexto2));
    }

    public function test_to_string_returns_centroEncargadoTexto_value()
    {
        $centroEncargadoTexto = new CentroEncargadoTexto('test value');
        $this->assertEquals('test value', (string)$centroEncargadoTexto);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $centroEncargadoTexto = CentroEncargadoTexto::fromNullableString('test value');
        $this->assertInstanceOf(CentroEncargadoTexto::class, $centroEncargadoTexto);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $centroEncargadoTexto = CentroEncargadoTexto::fromNullableString(null);
        $this->assertNull($centroEncargadoTexto);
    }

}
