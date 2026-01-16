<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TipoTelecoName;
use Tests\myTest;

class TipoTelecoNameTest extends myTest
{
    public function test_create_valid_tipoTelecoName()
    {
        $tipoTelecoName = new TipoTelecoName('test value');
        $this->assertEquals('test value', $tipoTelecoName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoTelecoName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoTelecoName()
    {
        $tipoTelecoName1 = new TipoTelecoName('test value');
        $tipoTelecoName2 = new TipoTelecoName('test value');
        $this->assertTrue($tipoTelecoName1->equals($tipoTelecoName2));
    }

    public function test_equals_returns_false_for_different_tipoTelecoName()
    {
        $tipoTelecoName1 = new TipoTelecoName('test value');
        $tipoTelecoName2 = new TipoTelecoName('alternative value');
        $this->assertFalse($tipoTelecoName1->equals($tipoTelecoName2));
    }

    public function test_to_string_returns_tipoTelecoName_value()
    {
        $tipoTelecoName = new TipoTelecoName('test value');
        $this->assertEquals('test value', (string)$tipoTelecoName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoTelecoName = TipoTelecoName::fromNullableString('test value');
        $this->assertInstanceOf(TipoTelecoName::class, $tipoTelecoName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoTelecoName = TipoTelecoName::fromNullableString(null);
        $this->assertNull($tipoTelecoName);
    }

}
