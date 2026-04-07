<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\IncCode;
use Tests\myTest;

class IncCodeTest extends myTest
{
    //IncCode must be at most 2 characters
    public function test_create_valid_incCode()
    {
        $incCode = new IncCode('fl');
        $this->assertEquals('fl', $incCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new IncCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_incCode_value()
    {
        $incCode = new IncCode('fl');
        $this->assertEquals('fl', (string)$incCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $incCode = IncCode::fromNullableString('fl');
        $this->assertInstanceOf(IncCode::class, $incCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $incCode = IncCode::fromNullableString(null);
        $this->assertNull($incCode);
    }

}
