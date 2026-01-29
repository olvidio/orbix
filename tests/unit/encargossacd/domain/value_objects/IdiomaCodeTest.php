<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\LocaleCode;
use Tests\myTest;

class IdiomaCodeTest extends myTest
{
    public function test_create_valid_idiomaCode()
    {
        $idiomaCode = new LocaleCode('test value');
        $this->assertEquals('test value', $idiomaCode->value());
    }

    public function test_equals_returns_true_for_same_idiomaCode()
    {
        $idiomaCode1 = new LocaleCode('test value');
        $idiomaCode2 = new LocaleCode('test value');
        $this->assertTrue($idiomaCode1->equals($idiomaCode2));
    }

    public function test_equals_returns_false_for_different_idiomaCode()
    {
        $idiomaCode1 = new LocaleCode('test value');
        $idiomaCode2 = new LocaleCode('alternative value');
        $this->assertFalse($idiomaCode1->equals($idiomaCode2));
    }

    public function test_to_string_returns_idiomaCode_value()
    {
        $idiomaCode = new LocaleCode('test value');
        $this->assertEquals('test value', (string)$idiomaCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $idiomaCode = LocaleCode::fromNullableString('test value');
        $this->assertInstanceOf(LocaleCode::class, $idiomaCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $idiomaCode = LocaleCode::fromNullableString(null);
        $this->assertNull($idiomaCode);
    }

}
