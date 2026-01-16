<?php

namespace Tests\unit\actividadtarifas\domain\value_objects;

use src\actividadtarifas\domain\value_objects\TarifaLetraCode;
use Tests\myTest;

class TarifaLetraCodeTest extends myTest
{
    // OJO TarifaLetraCode debe tener un máximo de 6 carácteres
    public function test_create_valid_tarifaLetraCode()
    {
        $tarifaLetraCode = new TarifaLetraCode('test');
        $this->assertEquals('TEST', $tarifaLetraCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TarifaLetraCode(str_repeat('a', 10)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tarifaLetraCode()
    {
        $tarifaLetraCode1 = new TarifaLetraCode('test');
        $tarifaLetraCode2 = new TarifaLetraCode('test');
        $this->assertTrue($tarifaLetraCode1->equals($tarifaLetraCode2));
    }

    public function test_equals_returns_false_for_different_tarifaLetraCode()
    {
        $tarifaLetraCode1 = new TarifaLetraCode('test');
        $tarifaLetraCode2 = new TarifaLetraCode('alte');
        $this->assertFalse($tarifaLetraCode1->equals($tarifaLetraCode2));
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tarifaLetraCode = TarifaLetraCode::fromNullableString('test');
        $this->assertInstanceOf(TarifaLetraCode::class, $tarifaLetraCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tarifaLetraCode = TarifaLetraCode::fromNullableString(null);
        $this->assertNull($tarifaLetraCode);
    }

}
