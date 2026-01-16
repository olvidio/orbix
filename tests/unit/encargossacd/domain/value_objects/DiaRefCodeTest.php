<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\DiaRefCode;
use Tests\myTest;

class DiaRefCodeTest extends myTest
{
    public function test_create_valid_diaRefCode()
    {
        $diaRefCode = new DiaRefCode('test value');
        $this->assertEquals('test value', $diaRefCode->value());
    }

    public function test_equals_returns_true_for_same_diaRefCode()
    {
        $diaRefCode1 = new DiaRefCode('test value');
        $diaRefCode2 = new DiaRefCode('test value');
        $this->assertTrue($diaRefCode1->equals($diaRefCode2));
    }

    public function test_equals_returns_false_for_different_diaRefCode()
    {
        $diaRefCode1 = new DiaRefCode('test value');
        $diaRefCode2 = new DiaRefCode('alternative value');
        $this->assertFalse($diaRefCode1->equals($diaRefCode2));
    }

    public function test_to_string_returns_diaRefCode_value()
    {
        $diaRefCode = new DiaRefCode('test value');
        $this->assertEquals('test value', (string)$diaRefCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $diaRefCode = DiaRefCode::fromNullableString('test value');
        $this->assertInstanceOf(DiaRefCode::class, $diaRefCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $diaRefCode = DiaRefCode::fromNullableString(null);
        $this->assertNull($diaRefCode);
    }

}
