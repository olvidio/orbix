<?php

namespace Tests\unit\cambios\domain\value_objects;

use src\cambios\domain\value_objects\PropiedadNombre;
use Tests\myTest;

class PropiedadNombreTest extends myTest
{
    public function test_create_valid_propiedadNombre()
    {
        $propiedadNombre = new PropiedadNombre('test value');
        $this->assertEquals('test value', $propiedadNombre->value());
    }

    public function test_equals_returns_true_for_same_propiedadNombre()
    {
        $propiedadNombre1 = new PropiedadNombre('test value');
        $propiedadNombre2 = new PropiedadNombre('test value');
        $this->assertTrue($propiedadNombre1->equals($propiedadNombre2));
    }

    public function test_equals_returns_false_for_different_propiedadNombre()
    {
        $propiedadNombre1 = new PropiedadNombre('test value');
        $propiedadNombre2 = new PropiedadNombre('alternative value');
        $this->assertFalse($propiedadNombre1->equals($propiedadNombre2));
    }

    public function test_to_string_returns_propiedadNombre_value()
    {
        $propiedadNombre = new PropiedadNombre('test value');
        $this->assertEquals('test value', (string)$propiedadNombre);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $propiedadNombre = PropiedadNombre::fromNullableString('test value');
        $this->assertInstanceOf(PropiedadNombre::class, $propiedadNombre);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $propiedadNombre = PropiedadNombre::fromNullableString(null);
        $this->assertNull($propiedadNombre);
    }

}
