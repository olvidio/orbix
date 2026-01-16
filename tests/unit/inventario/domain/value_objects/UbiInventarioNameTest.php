<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\UbiInventarioName;
use Tests\myTest;

class UbiInventarioNameTest extends myTest
{
    public function test_create_valid_ubiInventarioName()
    {
        $ubiInventarioName = new UbiInventarioName('test value');
        $this->assertEquals('test value', $ubiInventarioName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UbiInventarioName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_ubiInventarioName()
    {
        $ubiInventarioName1 = new UbiInventarioName('test value');
        $ubiInventarioName2 = new UbiInventarioName('test value');
        $this->assertTrue($ubiInventarioName1->equals($ubiInventarioName2));
    }

    public function test_equals_returns_false_for_different_ubiInventarioName()
    {
        $ubiInventarioName1 = new UbiInventarioName('test value');
        $ubiInventarioName2 = new UbiInventarioName('alternative value');
        $this->assertFalse($ubiInventarioName1->equals($ubiInventarioName2));
    }

    public function test_to_string_returns_ubiInventarioName_value()
    {
        $ubiInventarioName = new UbiInventarioName('test value');
        $this->assertEquals('test value', (string)$ubiInventarioName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $ubiInventarioName = UbiInventarioName::fromNullableString('test value');
        $this->assertInstanceOf(UbiInventarioName::class, $ubiInventarioName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $ubiInventarioName = UbiInventarioName::fromNullableString(null);
        $this->assertNull($ubiInventarioName);
    }

}
