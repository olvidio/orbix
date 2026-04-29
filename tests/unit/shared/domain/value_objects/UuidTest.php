<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\Exception\InvalidArgumentException as RamseyInvalidArgumentException;
use src\shared\domain\value_objects\Uuid;
use Tests\myTest;

final class UuidTest extends myTest
{
    private const VALID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_construct_valid_accepted(): void
    {
        $u = new Uuid(self::VALID);
        $this->assertSame(self::VALID, $u->value());
    }

    public function test_construct_invalid_uuid_throws(): void
    {
        $this->expectException(RamseyInvalidArgumentException::class);
        new Uuid('not-a-uuid');
    }

    public function test_equals_and_random(): void
    {
        $a = new Uuid(self::VALID);
        $b = new Uuid(self::VALID);
        $this->assertTrue($a->equals($b));

        $rand = RamseyUuid::uuid4()->toString();
        $this->assertNotSame(new Uuid($rand)->value(), self::VALID);
        $randomVo = Uuid::random();
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            strtolower($randomVo->value())
        );
    }

    public function test_to_string(): void
    {
        $u = new Uuid(self::VALID);
        $this->assertSame(self::VALID, (string) $u);
    }
}
