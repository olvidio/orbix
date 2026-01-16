<?php

namespace Tests\unit\actividadescentro\domain\value_objects;

use src\actividadescentro\domain\value_objects\CentroEncargadoOrden;
use Tests\myTest;

class CentroEncargadoOrdenTest extends myTest
{
    public function test_create_valid_centroEncargadoOrden()
    {
        $centroEncargadoOrden = new CentroEncargadoOrden(123);
        $this->assertEquals(123, $centroEncargadoOrden->value());
    }

    public function test_equals_returns_true_for_same_centroEncargadoOrden()
    {
        $centroEncargadoOrden1 = new CentroEncargadoOrden(123);
        $centroEncargadoOrden2 = new CentroEncargadoOrden(123);
        $this->assertTrue($centroEncargadoOrden1->equals($centroEncargadoOrden2));
    }

    public function test_equals_returns_false_for_different_centroEncargadoOrden()
    {
        $centroEncargadoOrden1 = new CentroEncargadoOrden(123);
        $centroEncargadoOrden2 = new CentroEncargadoOrden(456);
        $this->assertFalse($centroEncargadoOrden1->equals($centroEncargadoOrden2));
    }

    public function test_to_string_returns_centroEncargadoOrden_value()
    {
        $centroEncargadoOrden = new CentroEncargadoOrden(123);
        $this->assertEquals(123, (string)$centroEncargadoOrden);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $centroEncargadoOrden = CentroEncargadoOrden::fromNullableInt(123);
        $this->assertInstanceOf(CentroEncargadoOrden::class, $centroEncargadoOrden);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $centroEncargadoOrden = CentroEncargadoOrden::fromNullableInt(null);
        $this->assertNull($centroEncargadoOrden);
    }

}
