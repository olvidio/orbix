<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\LugarPublicacionName;
use Tests\myTest;

class LugarPublicacionNameTest extends myTest
{
    public function test_create_valid_lugarPublicacionName()
    {
        $lugarPublicacionName = new LugarPublicacionName('test value');
        $this->assertEquals('test value', $lugarPublicacionName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new LugarPublicacionName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_lugarPublicacionName_value()
    {
        $lugarPublicacionName = new LugarPublicacionName('test value');
        $this->assertEquals('test value', (string)$lugarPublicacionName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $lugarPublicacionName = LugarPublicacionName::fromNullableString('test value');
        $this->assertInstanceOf(LugarPublicacionName::class, $lugarPublicacionName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $lugarPublicacionName = LugarPublicacionName::fromNullableString(null);
        $this->assertNull($lugarPublicacionName);
    }

}
