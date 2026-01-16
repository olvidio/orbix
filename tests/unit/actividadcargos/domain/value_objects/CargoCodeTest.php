<?php

namespace Tests\unit\actividadcargos\domain\value_objects;

use src\actividadcargos\domain\value_objects\CargoCode;
use Tests\myTest;

class CargoCodeTest extends myTest
{
    // OJO No puede tener más de 8 caracteres
    public function test_create_valid_cargoCode()
    {
        $cargoCode = new CargoCode('test');
        $this->assertEquals('test', $cargoCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CargoCode(str_repeat('a', 100)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_cargoCode()
    {
        $cargoCode1 = new CargoCode('test');
        $cargoCode2 = new CargoCode('test');
        $this->assertTrue($cargoCode1->equals($cargoCode2));
    }

    public function test_equals_returns_false_for_different_cargoCode()
    {
        $cargoCode1 = new CargoCode('test');
        $cargoCode2 = new CargoCode('alternat');
        $this->assertFalse($cargoCode1->equals($cargoCode2));
    }

    public function test_to_string_returns_cargoCode_value()
    {
        $cargoCode = new CargoCode('test');
        $this->assertEquals('test', (string)$cargoCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $cargoCode = CargoCode::fromNullableString('test');
        $this->assertInstanceOf(CargoCode::class, $cargoCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $cargoCode = CargoCode::fromNullableString(null);
        $this->assertNull($cargoCode);
    }

}
