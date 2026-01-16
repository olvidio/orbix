<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\Plazas;
use Tests\myTest;

class PlazasTest extends myTest
{
    public function test_create_valid_plazas()
    {
        $plazas = new Plazas(123);
        $this->assertEquals(123, $plazas->value());
    }

    public function test_equals_returns_true_for_same_plazas()
    {
        $plazas1 = new Plazas(123);
        $plazas2 = new Plazas(123);
        $this->assertTrue($plazas1->equals($plazas2));
    }

    public function test_equals_returns_false_for_different_plazas()
    {
        $plazas1 = new Plazas(123);
        $plazas2 = new Plazas(456);
        $this->assertFalse($plazas1->equals($plazas2));
    }

    public function test_to_string_returns_plazas_value()
    {
        $plazas = new Plazas(123);
        $this->assertEquals(123, (string)$plazas);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $plazas = Plazas::fromNullableInt(123);
        $this->assertInstanceOf(Plazas::class, $plazas);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $plazas = Plazas::fromNullableInt(null);
        $this->assertNull($plazas);
    }

}
