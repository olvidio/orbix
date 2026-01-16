<?php

namespace Tests\unit\actividadcargos\domain\value_objects;

use src\actividadcargos\domain\value_objects\TipoCargoCode;
use Tests\myTest;

class TipoCargoCodeTest extends myTest
{
    public function test_create_valid_tipoCargoCode()
    {
        $tipoCargoCode = new TipoCargoCode(TipoCargoCode::EMPTY);
        $this->assertEquals(TipoCargoCode::EMPTY, $tipoCargoCode->value());
    }

    public function test_invalid_tipoCargoCode_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoCargoCode('invalid_value');
    }

    public function test_equals_returns_true_for_same_tipoCargoCode()
    {
        $tipoCargoCode1 = new TipoCargoCode(TipoCargoCode::EMPTY);
        $tipoCargoCode2 = new TipoCargoCode(TipoCargoCode::EMPTY);
        $this->assertTrue($tipoCargoCode1->equals($tipoCargoCode2));
    }

    public function test_equals_returns_false_for_different_tipoCargoCode()
    {
        $tipoCargoCode1 = new TipoCargoCode(TipoCargoCode::EMPTY);
        $tipoCargoCode2 = new TipoCargoCode(TipoCargoCode::D);
        $this->assertFalse($tipoCargoCode1->equals($tipoCargoCode2));
    }

    public function test_to_string_returns_tipoCargoCode_value()
    {
        $tipoCargoCode = new TipoCargoCode('sacd');
        $this->assertEquals('sacd', (string)$tipoCargoCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoCargoCode = TipoCargoCode::fromNullableString(TipoCargoCode::SD);
        $this->assertInstanceOf(TipoCargoCode::class, $tipoCargoCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoCargoCode = TipoCargoCode::fromNullableString(null);
        $this->assertNull($tipoCargoCode);
    }

}
