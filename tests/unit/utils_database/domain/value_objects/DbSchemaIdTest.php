<?php

namespace Tests\unit\utils_database\domain\value_objects;

use src\utils_database\domain\value_objects\DbSchemaId;
use Tests\myTest;

class DbSchemaIdTest extends myTest
{
    public function test_create_valid_dbSchemaId()
    {
        $dbSchemaId = new DbSchemaId(123);
        $this->assertEquals(123, $dbSchemaId->value());
    }

    public function test_equals_returns_true_for_same_dbSchemaId()
    {
        $dbSchemaId1 = new DbSchemaId(123);
        $dbSchemaId2 = new DbSchemaId(123);
        $this->assertTrue($dbSchemaId1->equals($dbSchemaId2));
    }

    public function test_equals_returns_false_for_different_dbSchemaId()
    {
        $dbSchemaId1 = new DbSchemaId(123);
        $dbSchemaId2 = new DbSchemaId(456);
        $this->assertFalse($dbSchemaId1->equals($dbSchemaId2));
    }

    public function test_to_string_returns_dbSchemaId_value()
    {
        $dbSchemaId = new DbSchemaId(123);
        $this->assertEquals(123, (string)$dbSchemaId);
    }

}
