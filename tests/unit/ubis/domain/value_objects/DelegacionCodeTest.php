<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;

class DelegacionCodeTest extends myTest
{
    public function test_create_valid_delegacionCode()
    {
        $delegacionCode = new DelegacionCode('dlb');
        $this->assertEquals('dlb', $delegacionCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DelegacionCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_delegacionCode()
    {
        $delegacionCode1 = new DelegacionCode('dlb');
        $delegacionCode2 = new DelegacionCode('dlb');
        $this->assertTrue($delegacionCode1->equals($delegacionCode2));
    }

    public function test_equals_returns_false_for_different_delegacionCode()
    {
        $delegacionCode1 = new DelegacionCode('dlb');
        $delegacionCode2 = new DelegacionCode('dls');
        $this->assertFalse($delegacionCode1->equals($delegacionCode2));
    }

    public function test_to_string_returns_delegacionCode_value()
    {
        $delegacionCode = new DelegacionCode('dlb');
        $this->assertEquals('dlb', (string)$delegacionCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $delegacionCode = DelegacionCode::fromNullableString('dlb');
        $this->assertInstanceOf(DelegacionCode::class, $delegacionCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $delegacionCode = DelegacionCode::fromNullableString(null);
        $this->assertNull($delegacionCode);
    }

}
