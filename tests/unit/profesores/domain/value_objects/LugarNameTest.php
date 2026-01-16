<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\LugarName;
use Tests\myTest;

class LugarNameTest extends myTest
{
    public function test_create_valid_lugarName()
    {
        $lugarName = new LugarName('test value');
        $this->assertEquals('test value', $lugarName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new LugarName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_lugarName_value()
    {
        $lugarName = new LugarName('test value');
        $this->assertEquals('test value', (string)$lugarName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $lugarName = LugarName::fromNullableString('test value');
        $this->assertInstanceOf(LugarName::class, $lugarName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $lugarName = LugarName::fromNullableString(null);
        $this->assertNull($lugarName);
    }

}
