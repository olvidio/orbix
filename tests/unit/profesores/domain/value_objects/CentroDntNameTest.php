<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\CentroDntName;
use Tests\myTest;

class CentroDntNameTest extends myTest
{
    public function test_create_valid_centroDntName()
    {
        $centroDntName = new CentroDntName('test value');
        $this->assertEquals('test value', $centroDntName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CentroDntName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_centroDntName_value()
    {
        $centroDntName = new CentroDntName('test value');
        $this->assertEquals('test value', (string)$centroDntName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $centroDntName = CentroDntName::fromNullableString('test value');
        $this->assertInstanceOf(CentroDntName::class, $centroDntName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $centroDntName = CentroDntName::fromNullableString(null);
        $this->assertNull($centroDntName);
    }

}
