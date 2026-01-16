<?php

namespace Tests\unit\cambios\domain\value_objects;

use src\cambios\domain\value_objects\OperadorPref;
use Tests\myTest;

class OperadorPrefTest extends myTest
{
    public function test_create_valid_operadorPref()
    {
        $operadorPref = new OperadorPref(OperadorPref::IGUAL);
        $this->assertEquals(OperadorPref::IGUAL, $operadorPref->value());
    }

    public function test_invalid_operadorPref_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new OperadorPref('invalid_value');
    }

    public function test_equals_returns_true_for_same_operadorPref()
    {
        $operadorPref1 = new OperadorPref(OperadorPref::IGUAL);
        $operadorPref2 = new OperadorPref(OperadorPref::IGUAL);
        $this->assertTrue($operadorPref1->equals($operadorPref2));
    }

    public function test_equals_returns_false_for_different_operadorPref()
    {
        $operadorPref1 = new OperadorPref(OperadorPref::IGUAL);
        $operadorPref2 = new OperadorPref(OperadorPref::MAYOR);
        $this->assertFalse($operadorPref1->equals($operadorPref2));
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $operadorPref = OperadorPref::fromNullableString(OperadorPref::IGUAL);
        $this->assertInstanceOf(OperadorPref::class, $operadorPref);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $operadorPref = OperadorPref::fromNullableString(null);
        $this->assertNull($operadorPref);
    }

}
