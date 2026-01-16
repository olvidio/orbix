<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\MasMenosCode;
use Tests\myTest;

class MasMenosCodeTest extends myTest
{
    public function test_create_valid_masMenosCode()
    {
        $masMenosCode = new MasMenosCode('test value');
        $this->assertEquals('test value', $masMenosCode->value());
    }

    public function test_equals_returns_true_for_same_masMenosCode()
    {
        $masMenosCode1 = new MasMenosCode('test value');
        $masMenosCode2 = new MasMenosCode('test value');
        $this->assertTrue($masMenosCode1->equals($masMenosCode2));
    }

    public function test_equals_returns_false_for_different_masMenosCode()
    {
        $masMenosCode1 = new MasMenosCode('test value');
        $masMenosCode2 = new MasMenosCode('alternative value');
        $this->assertFalse($masMenosCode1->equals($masMenosCode2));
    }

    public function test_to_string_returns_masMenosCode_value()
    {
        $masMenosCode = new MasMenosCode('test value');
        $this->assertEquals('test value', (string)$masMenosCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $masMenosCode = MasMenosCode::fromNullableString('test value');
        $this->assertInstanceOf(MasMenosCode::class, $masMenosCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $masMenosCode = MasMenosCode::fromNullableString(null);
        $this->assertNull($masMenosCode);
    }

}
