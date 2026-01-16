<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\NivelStgrDesc;
use Tests\myTest;

class NivelStgrDescTest extends myTest
{
    public function test_create_valid_nivelStgrDesc()
    {
        $nivelStgrDesc = new NivelStgrDesc('test value');
        $this->assertEquals('test value', $nivelStgrDesc->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new NivelStgrDesc(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_nivelStgrDesc()
    {
        $nivelStgrDesc1 = new NivelStgrDesc('test value');
        $nivelStgrDesc2 = new NivelStgrDesc('test value');
        $this->assertTrue($nivelStgrDesc1->equals($nivelStgrDesc2));
    }

    public function test_equals_returns_false_for_different_nivelStgrDesc()
    {
        $nivelStgrDesc1 = new NivelStgrDesc('test value');
        $nivelStgrDesc2 = new NivelStgrDesc('alternative value');
        $this->assertFalse($nivelStgrDesc1->equals($nivelStgrDesc2));
    }

    public function test_to_string_returns_nivelStgrDesc_value()
    {
        $nivelStgrDesc = new NivelStgrDesc('test value');
        $this->assertEquals('test value', (string)$nivelStgrDesc);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $nivelStgrDesc = NivelStgrDesc::fromNullableString('test value');
        $this->assertInstanceOf(NivelStgrDesc::class, $nivelStgrDesc);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $nivelStgrDesc = NivelStgrDesc::fromNullableString(null);
        $this->assertNull($nivelStgrDesc);
    }

}
