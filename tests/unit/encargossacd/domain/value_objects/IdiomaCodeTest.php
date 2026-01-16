<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\IdiomaCode;
use Tests\myTest;

class IdiomaCodeTest extends myTest
{
    public function test_create_valid_idiomaCode()
    {
        $idiomaCode = new IdiomaCode('test value');
        $this->assertEquals('test value', $idiomaCode->value());
    }

    public function test_equals_returns_true_for_same_idiomaCode()
    {
        $idiomaCode1 = new IdiomaCode('test value');
        $idiomaCode2 = new IdiomaCode('test value');
        $this->assertTrue($idiomaCode1->equals($idiomaCode2));
    }

    public function test_equals_returns_false_for_different_idiomaCode()
    {
        $idiomaCode1 = new IdiomaCode('test value');
        $idiomaCode2 = new IdiomaCode('alternative value');
        $this->assertFalse($idiomaCode1->equals($idiomaCode2));
    }

    public function test_to_string_returns_idiomaCode_value()
    {
        $idiomaCode = new IdiomaCode('test value');
        $this->assertEquals('test value', (string)$idiomaCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $idiomaCode = IdiomaCode::fromNullableString('test value');
        $this->assertInstanceOf(IdiomaCode::class, $idiomaCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $idiomaCode = IdiomaCode::fromNullableString(null);
        $this->assertNull($idiomaCode);
    }

}
