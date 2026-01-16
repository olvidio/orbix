<?php

namespace Tests\unit\actividadcargos\domain\value_objects;

use src\actividadcargos\domain\value_objects\ObservacionesCargo;
use Tests\myTest;

class ObservacionesCargoTest extends myTest
{
    public function test_create_valid_observacionesCargo()
    {
        $observacionesCargo = new ObservacionesCargo('test value');
        $this->assertEquals('test value', $observacionesCargo->value());
    }

    public function test_equals_returns_true_for_same_observacionesCargo()
    {
        $observacionesCargo1 = new ObservacionesCargo('test value');
        $observacionesCargo2 = new ObservacionesCargo('test value');
        $this->assertTrue($observacionesCargo1->equals($observacionesCargo2));
    }

    public function test_equals_returns_false_for_different_observacionesCargo()
    {
        $observacionesCargo1 = new ObservacionesCargo('test value');
        $observacionesCargo2 = new ObservacionesCargo('alternative value');
        $this->assertFalse($observacionesCargo1->equals($observacionesCargo2));
    }

    public function test_to_string_returns_observacionesCargo_value()
    {
        $observacionesCargo = new ObservacionesCargo('test value');
        $this->assertEquals('test value', (string)$observacionesCargo);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observacionesCargo = ObservacionesCargo::fromNullableString('test value');
        $this->assertInstanceOf(ObservacionesCargo::class, $observacionesCargo);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observacionesCargo = ObservacionesCargo::fromNullableString(null);
        $this->assertNull($observacionesCargo);
    }

}
