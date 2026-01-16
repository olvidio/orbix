<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use Tests\myTest;

class AnuncioIdTest extends myTest
{
    public function test_create_valid_anuncioId()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $anuncioId = new AnuncioId($uuid);
        $this->assertEquals($uuid, $anuncioId->value());
    }

    public function test_create_anuncioId_with_invalid_uuid_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        new AnuncioId('invalid-uuid');
    }

    public function test_random_creates_valid_anuncioId()
    {
        $anuncioId = AnuncioId::random();
        $this->assertNotEmpty($anuncioId->value());
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $anuncioId->value());
    }

    public function test_fromString_creates_valid_anuncioId()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $anuncioId = AnuncioId::fromString($uuid);
        $this->assertEquals($uuid, $anuncioId->value());
    }

    public function test_equals_returns_true_for_same_uuid()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $anuncioId1 = new AnuncioId($uuid);
        $anuncioId2 = new AnuncioId($uuid);
        $this->assertTrue($anuncioId1->equals($anuncioId2));
    }

    public function test_equals_returns_false_for_different_uuid()
    {
        $anuncioId1 = new AnuncioId('550e8400-e29b-41d4-a716-446655440000');
        $anuncioId2 = new AnuncioId('660e8400-e29b-41d4-a716-446655440000');
        $this->assertFalse($anuncioId1->equals($anuncioId2));
    }

    public function test_toString_returns_uuid_value()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $anuncioId = new AnuncioId($uuid);
        $this->assertEquals($uuid, (string)$anuncioId);
    }

}
