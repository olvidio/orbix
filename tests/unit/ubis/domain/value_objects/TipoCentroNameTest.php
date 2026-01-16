<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TipoCentroName;
use Tests\myTest;

class TipoCentroNameTest extends myTest
{
    public function test_create_valid_tipoCentroName()
    {
        $tipoCentroName = new TipoCentroName('test value');
        $this->assertEquals('test value', $tipoCentroName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoCentroName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoCentroName()
    {
        $tipoCentroName1 = new TipoCentroName('test value');
        $tipoCentroName2 = new TipoCentroName('test value');
        $this->assertTrue($tipoCentroName1->equals($tipoCentroName2));
    }

    public function test_equals_returns_false_for_different_tipoCentroName()
    {
        $tipoCentroName1 = new TipoCentroName('test value');
        $tipoCentroName2 = new TipoCentroName('alternative value');
        $this->assertFalse($tipoCentroName1->equals($tipoCentroName2));
    }

    public function test_to_string_returns_tipoCentroName_value()
    {
        $tipoCentroName = new TipoCentroName('test value');
        $this->assertEquals('test value', (string)$tipoCentroName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoCentroName = TipoCentroName::fromNullableString('test value');
        $this->assertInstanceOf(TipoCentroName::class, $tipoCentroName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoCentroName = TipoCentroName::fromNullableString(null);
        $this->assertNull($tipoCentroName);
    }

}
