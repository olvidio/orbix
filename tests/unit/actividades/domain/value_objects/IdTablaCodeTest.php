<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\IdTablaCode;
use Tests\myTest;

class IdTablaCodeTest extends myTest
{
    public function test_create_valid_idTablaCode()
    {
        $idTablaCode = new IdTablaCode(IdTablaCode::DL);
        $this->assertEquals(IdTablaCode::DL, $idTablaCode->value());
    }

    public function test_invalid_idTablaCode_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new IdTablaCode('invalid_value');
    }

    public function test_equals_returns_true_for_same_idTablaCode()
    {
        $idTablaCode1 = new IdTablaCode(IdTablaCode::DL);
        $idTablaCode2 = new IdTablaCode(IdTablaCode::DL);
        $this->assertTrue($idTablaCode1->equals($idTablaCode2));
    }

    public function test_equals_returns_false_for_different_idTablaCode()
    {
        $idTablaCode1 = new IdTablaCode(IdTablaCode::DL);
        $idTablaCode2 = new IdTablaCode(IdTablaCode::EX);
        $this->assertFalse($idTablaCode1->equals($idTablaCode2));
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $idTablaCode = IdTablaCode::fromNullableString(IdTablaCode::DL);
        $this->assertInstanceOf(IdTablaCode::class, $idTablaCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $idTablaCode = IdTablaCode::fromNullableString(null);
        $this->assertNull($idTablaCode);
    }

}
