<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\YearNumber;
use Tests\myTest;

class YearNumberTest extends myTest
{
    public function test_create_valid_yearNumber()
    {
        $yearNumber = new YearNumber(123);
        $this->assertEquals(123, $yearNumber->value());
    }

    public function test_to_string_returns_yearNumber_value()
    {
        $yearNumber = new YearNumber(123);
        $this->assertEquals(123, (string)$yearNumber);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $yearNumber = YearNumber::fromNullableInt(123);
        $this->assertInstanceOf(YearNumber::class, $yearNumber);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $yearNumber = YearNumber::fromNullableInt(null);
        $this->assertNull($yearNumber);
    }

}
