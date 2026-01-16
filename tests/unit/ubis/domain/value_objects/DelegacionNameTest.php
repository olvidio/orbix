<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DelegacionName;
use Tests\myTest;

class DelegacionNameTest extends myTest
{
    public function test_create_valid_delegacionName()
    {
        $delegacionName = new DelegacionName('test value');
        $this->assertEquals('test value', $delegacionName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DelegacionName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_delegacionName()
    {
        $delegacionName1 = new DelegacionName('test value');
        $delegacionName2 = new DelegacionName('test value');
        $this->assertTrue($delegacionName1->equals($delegacionName2));
    }

    public function test_equals_returns_false_for_different_delegacionName()
    {
        $delegacionName1 = new DelegacionName('test value');
        $delegacionName2 = new DelegacionName('alternative value');
        $this->assertFalse($delegacionName1->equals($delegacionName2));
    }

    public function test_to_string_returns_delegacionName_value()
    {
        $delegacionName = new DelegacionName('test value');
        $this->assertEquals('test value', (string)$delegacionName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $delegacionName = DelegacionName::fromNullableString('test value');
        $this->assertInstanceOf(DelegacionName::class, $delegacionName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $delegacionName = DelegacionName::fromNullableString(null);
        $this->assertNull($delegacionName);
    }

}
