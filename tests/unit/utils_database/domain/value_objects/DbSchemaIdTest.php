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

    public function test_resto_reserved_ids_are_valid(): void
    {
        $this->assertSame(-1001, (new DbSchemaId(-1001))->value());
        $this->assertSame(-2001, (new DbSchemaId(-2001))->value());
        $this->assertSame(-3001, (new DbSchemaId(-3001))->value());
    }

    public function test_other_non_positive_ids_throw(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DbSchemaId(-1002);
    }

    public function test_from_string_accepts_signed_integers(): void
    {
        $this->assertSame(-2001, DbSchemaId::fromString('-2001')->value());
        $this->assertSame(123, DbSchemaId::fromString('123')->value());
    }

}
