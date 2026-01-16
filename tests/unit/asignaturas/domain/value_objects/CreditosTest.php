<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\Creditos;
use Tests\myTest;

class CreditosTest extends myTest
{
    public function test_create_valid_creditos()
    {
        $creditos = new Creditos(3.5);
        $this->assertEquals(3.5, $creditos->value());
    }

    public function test_equals_returns_true_for_same_creditos()
    {
        $creditos1 = new Creditos(3.5);
        $creditos2 = new Creditos(3.5);
        $this->assertTrue($creditos1->equals($creditos2));
    }

    public function test_equals_returns_false_for_different_creditos()
    {
        $creditos1 = new Creditos(3.5);
        $creditos2 = new Creditos(4.99);
        $this->assertFalse($creditos1->equals($creditos2));
    }

    public function test_to_string_returns_creditos_value()
    {
        $creditos = new Creditos(3.5);
        $this->assertEquals(3.5, (string)$creditos);
    }

    public function test_fromNullableFloat_returns_instance_for_valid_value()
    {
        $creditos = Creditos::fromNullableFloat(3.5);
        $this->assertInstanceOf(Creditos::class, $creditos);
    }

    public function test_fromNullableFloat_returns_null_for_null_value()
    {
        $creditos = Creditos::fromNullableFloat(null);
        $this->assertNull($creditos);
    }

}
