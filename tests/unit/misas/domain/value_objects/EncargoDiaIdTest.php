<?php

namespace Tests\unit\misas\domain\value_objects;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use src\misas\domain\value_objects\EncargoDiaId;
use Tests\myTest;

class EncargoDiaIdTest extends myTest
{
    public function test_create_valid_encargoDiaId()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $encargoDiaId = new EncargoDiaId($uuid);
        $this->assertEquals($uuid, $encargoDiaId->value());
    }

    public function test_create_encargoDiaId_with_invalid_uuid_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        new EncargoDiaId('invalid-uuid');
    }

    public function test_random_creates_valid_encargoDiaId()
    {
        $encargoDiaId = EncargoDiaId::random();
        $this->assertNotEmpty($encargoDiaId->value());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $encargoDiaId->value()
        );
    }

    public function test_fromString_creates_valid_encargoDiaId()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $encargoDiaId = EncargoDiaId::fromString($uuid);
        $this->assertEquals($uuid, $encargoDiaId->value());
    }

    public function test_equals_returns_true_for_same_uuid()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $encargoDiaId1 = new EncargoDiaId($uuid);
        $encargoDiaId2 = new EncargoDiaId($uuid);
        $this->assertTrue($encargoDiaId1->equals($encargoDiaId2));
    }

    public function test_equals_returns_false_for_different_uuid()
    {
        $encargoDiaId1 = new EncargoDiaId('550e8400-e29b-41d4-a716-446655440000');
        $encargoDiaId2 = new EncargoDiaId('660e8400-e29b-41d4-a716-446655440000');
        $this->assertFalse($encargoDiaId1->equals($encargoDiaId2));
    }

    public function test_toString_returns_uuid_value()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $encargoDiaId = new EncargoDiaId($uuid);
        $this->assertEquals($uuid, (string) $encargoDiaId);
    }
}
