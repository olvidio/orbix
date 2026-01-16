<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\TipoDocName;
use Tests\myTest;

class TipoDocNameTest extends myTest
{
    public function test_create_valid_tipoDocName()
    {
        $tipoDocName = new TipoDocName('test value');
        $this->assertEquals('test value', $tipoDocName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDocName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoDocName()
    {
        $tipoDocName1 = new TipoDocName('test value');
        $tipoDocName2 = new TipoDocName('test value');
        $this->assertTrue($tipoDocName1->equals($tipoDocName2));
    }

    public function test_equals_returns_false_for_different_tipoDocName()
    {
        $tipoDocName1 = new TipoDocName('test value');
        $tipoDocName2 = new TipoDocName('alternative value');
        $this->assertFalse($tipoDocName1->equals($tipoDocName2));
    }

    public function test_to_string_returns_tipoDocName_value()
    {
        $tipoDocName = new TipoDocName('test value');
        $this->assertEquals('test value', (string)$tipoDocName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoDocName = TipoDocName::fromNullableString('test value');
        $this->assertInstanceOf(TipoDocName::class, $tipoDocName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoDocName = TipoDocName::fromNullableString(null);
        $this->assertNull($tipoDocName);
    }

}
