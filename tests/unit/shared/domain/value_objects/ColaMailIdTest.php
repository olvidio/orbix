<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use Ramsey\Uuid\Exception\InvalidArgumentException as RamseyInvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use src\shared\domain\value_objects\ColaMailId;
use Tests\myTest;

final class ColaMailIdTest extends myTest
{
    private const VALID = '550e8400-e29b-41d4-a716-446655440001';

    public function test_constructor_and_from_string_accept_valid_uuid(): void
    {
        $id = ColaMailId::fromString(self::VALID);
        $this->assertSame(self::VALID, $id->value());
    }

    public function test_equals_and_random_are_valid_uuids(): void
    {
        $a = new ColaMailId(self::VALID);
        $b = new ColaMailId(self::VALID);
        $this->assertTrue($a->equals($b));

        $r = ColaMailId::random();
        $this->assertSame(36, strlen($r->value()));
        $this->assertTrue(RamseyUuid::isValid($r->value()));
    }

    public function test_constructor_rejects_bad_string(): void
    {
        $this->expectException(RamseyInvalidArgumentException::class);
        new ColaMailId('bad-uuid');
    }
}
