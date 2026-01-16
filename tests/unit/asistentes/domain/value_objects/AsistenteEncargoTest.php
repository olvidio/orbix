<?php

namespace Tests\unit\asistentes\domain\value_objects;

use src\asistentes\domain\value_objects\AsistenteEncargo;
use Tests\myTest;

class AsistenteEncargoTest extends myTest
{
    public function test_create_valid_asistenteEncargo()
    {
        $asistenteEncargo = new AsistenteEncargo('test value');
        $this->assertEquals('test value', $asistenteEncargo->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsistenteEncargo(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_asistenteEncargo_value()
    {
        $asistenteEncargo = new AsistenteEncargo('test value');
        $this->assertEquals('test value', (string)$asistenteEncargo);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asistenteEncargo = AsistenteEncargo::fromNullableString('test value');
        $this->assertInstanceOf(AsistenteEncargo::class, $asistenteEncargo);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asistenteEncargo = AsistenteEncargo::fromNullableString(null);
        $this->assertNull($asistenteEncargo);
    }

}
