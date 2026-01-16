<?php

namespace Tests\unit\cambios\domain\value_objects;

use src\cambios\domain\value_objects\CsvPauId;
use Tests\myTest;

class CsvPauIdTest extends myTest
{
    public function test_create_valid_csvPauId()
    {
        $csvPauId = new CsvPauId('test value');
        $this->assertEquals('test value', $csvPauId->value());
    }

    public function test_equals_returns_true_for_same_csvPauId()
    {
        $csvPauId1 = new CsvPauId('test value');
        $csvPauId2 = new CsvPauId('test value');
        $this->assertTrue($csvPauId1->equals($csvPauId2));
    }

    public function test_equals_returns_false_for_different_csvPauId()
    {
        $csvPauId1 = new CsvPauId('test value');
        $csvPauId2 = new CsvPauId('alternative value');
        $this->assertFalse($csvPauId1->equals($csvPauId2));
    }

    public function test_to_string_returns_csvPauId_value()
    {
        $csvPauId = new CsvPauId('test value');
        $this->assertEquals('test value', (string)$csvPauId);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $csvPauId = CsvPauId::fromNullableString('test value');
        $this->assertInstanceOf(CsvPauId::class, $csvPauId);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $csvPauId = CsvPauId::fromNullableString(null);
        $this->assertNull($csvPauId);
    }

}
