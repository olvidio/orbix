<?php

namespace Tests\unit\utils_database\domain\value_objects;

use src\utils_database\domain\value_objects\DbSchemaCode;
use Tests\myTest;

class DbSchemaCodeTest extends myTest
{
    public function test_create_valid_dbSchemaCode()
    {
        $dbSchemaCode = new DbSchemaCode('test value');
        $this->assertEquals('test value', $dbSchemaCode->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DbSchemaCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_dbSchemaCode()
    {
        $dbSchemaCode1 = new DbSchemaCode('test value');
        $dbSchemaCode2 = new DbSchemaCode('test value');
        $this->assertTrue($dbSchemaCode1->equals($dbSchemaCode2));
    }

    public function test_equals_returns_false_for_different_dbSchemaCode()
    {
        $dbSchemaCode1 = new DbSchemaCode('test value');
        $dbSchemaCode2 = new DbSchemaCode('alternative value');
        $this->assertFalse($dbSchemaCode1->equals($dbSchemaCode2));
    }

    public function test_to_string_returns_dbSchemaCode_value()
    {
        $dbSchemaCode = new DbSchemaCode('test value');
        $this->assertEquals('test value', (string)$dbSchemaCode);
    }

}
