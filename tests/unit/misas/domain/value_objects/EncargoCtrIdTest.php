<?php

namespace Tests\unit\misas\domain\value_objects;

use InvalidArgumentException;
use src\misas\domain\value_objects\EncargoCtrId;
use Tests\myTest;

class EncargoCtrIdTest extends myTest
{
    public function test_create_valid_encargoCtrId()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $encargoCtrId = new EncargoCtrId($uuid);
        $this->assertEquals($uuid, $encargoCtrId->value());
    }

    public function test_create_encargoCtrId_with_invalid_uuid_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        new EncargoCtrId('invalid-uuid');
    }

    public function test_random_creates_valid_encargoCtrId()
    {
        $encargoCtrId = EncargoCtrId::random();
        $this->assertNotEmpty($encargoCtrId->value());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $encargoCtrId->value()
        );
    }

    public function test_fromString_creates_valid_encargoCtrId()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $encargoCtrId = EncargoCtrId::fromString($uuid);
        $this->assertEquals($uuid, $encargoCtrId->value());
    }

    public function test_equals_returns_true_for_same_uuid()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $encargoCtrId1 = new EncargoCtrId($uuid);
        $encargoCtrId2 = new EncargoCtrId($uuid);
        $this->assertTrue($encargoCtrId1->equals($encargoCtrId2));
    }

    public function test_equals_returns_false_for_different_uuid()
    {
        $encargoCtrId1 = new EncargoCtrId('550e8400-e29b-41d4-a716-446655440000');
        $encargoCtrId2 = new EncargoCtrId('660e8400-e29b-41d4-a716-446655440000');
        $this->assertFalse($encargoCtrId1->equals($encargoCtrId2));
    }

    public function test_toString_returns_uuid_value()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $encargoCtrId = new EncargoCtrId($uuid);
        $this->assertEquals($uuid, (string) $encargoCtrId);
    }
}
