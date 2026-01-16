<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\CeNumber;
use Tests\myTest;

class CeNumberTest extends myTest
{
    public function test_create_valid_ceNumber()
    {
        $ceNumber = new CeNumber(123);
        $this->assertEquals(123, $ceNumber->value());
    }

    public function test_to_string_returns_ceNumber_value()
    {
        $ceNumber = new CeNumber(123);
        $this->assertEquals(123, (string)$ceNumber);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $ceNumber = CeNumber::fromNullableInt(123);
        $this->assertInstanceOf(CeNumber::class, $ceNumber);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $ceNumber = CeNumber::fromNullableInt(null);
        $this->assertNull($ceNumber);
    }

}
