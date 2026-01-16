<?php

namespace Tests\unit\actividadplazas\domain\value_objects;

use src\actividadplazas\domain\value_objects\DelegacionTablaCode;
use Tests\myTest;

class DelegacionTablaCodeTest extends myTest
{
    public function test_create_valid_delegacionTablaCode()
    {
        $delegacionTablaCode = new DelegacionTablaCode('test value');
        $this->assertEquals('test value', $delegacionTablaCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DelegacionTablaCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $delegacionTablaCode = DelegacionTablaCode::fromNullableString('test value');
        $this->assertInstanceOf(DelegacionTablaCode::class, $delegacionTablaCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $delegacionTablaCode = DelegacionTablaCode::fromNullableString(null);
        $this->assertNull($delegacionTablaCode);
    }

}
