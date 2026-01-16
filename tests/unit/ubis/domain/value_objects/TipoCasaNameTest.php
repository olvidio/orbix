<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TipoCasaName;
use Tests\myTest;

class TipoCasaNameTest extends myTest
{
    public function test_create_valid_tipoCasaName()
    {
        $tipoCasaName = new TipoCasaName('test value');
        $this->assertEquals('test value', $tipoCasaName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoCasaName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoCasaName()
    {
        $tipoCasaName1 = new TipoCasaName('test value');
        $tipoCasaName2 = new TipoCasaName('test value');
        $this->assertTrue($tipoCasaName1->equals($tipoCasaName2));
    }

    public function test_equals_returns_false_for_different_tipoCasaName()
    {
        $tipoCasaName1 = new TipoCasaName('test value');
        $tipoCasaName2 = new TipoCasaName('alternative value');
        $this->assertFalse($tipoCasaName1->equals($tipoCasaName2));
    }

    public function test_to_string_returns_tipoCasaName_value()
    {
        $tipoCasaName = new TipoCasaName('test value');
        $this->assertEquals('test value', (string)$tipoCasaName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoCasaName = TipoCasaName::fromNullableString('test value');
        $this->assertInstanceOf(TipoCasaName::class, $tipoCasaName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoCasaName = TipoCasaName::fromNullableString(null);
        $this->assertNull($tipoCasaName);
    }

}
