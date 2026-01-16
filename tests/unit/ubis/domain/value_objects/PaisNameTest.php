<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\PaisName;
use Tests\myTest;

class PaisNameTest extends myTest
{
    public function test_create_valid_paisName()
    {
        $paisName = new PaisName('test value');
        $this->assertEquals('test value', $paisName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PaisName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_paisName()
    {
        $paisName1 = new PaisName('test value');
        $paisName2 = new PaisName('test value');
        $this->assertTrue($paisName1->equals($paisName2));
    }

    public function test_equals_returns_false_for_different_paisName()
    {
        $paisName1 = new PaisName('test value');
        $paisName2 = new PaisName('alternative value');
        $this->assertFalse($paisName1->equals($paisName2));
    }

    public function test_to_string_returns_paisName_value()
    {
        $paisName = new PaisName('test value');
        $this->assertEquals('test value', (string)$paisName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $paisName = PaisName::fromNullableString('test value');
        $this->assertInstanceOf(PaisName::class, $paisName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $paisName = PaisName::fromNullableString(null);
        $this->assertNull($paisName);
    }

}
