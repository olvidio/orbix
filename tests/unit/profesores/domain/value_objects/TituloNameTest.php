<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\TituloName;
use Tests\myTest;

class TituloNameTest extends myTest
{
    public function test_create_valid_tituloName()
    {
        $tituloName = new TituloName('test value');
        $this->assertEquals('test value', $tituloName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TituloName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_tituloName_value()
    {
        $tituloName = new TituloName('test value');
        $this->assertEquals('test value', (string)$tituloName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tituloName = TituloName::fromNullableString('test value');
        $this->assertInstanceOf(TituloName::class, $tituloName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tituloName = TituloName::fromNullableString(null);
        $this->assertNull($tituloName);
    }

}
