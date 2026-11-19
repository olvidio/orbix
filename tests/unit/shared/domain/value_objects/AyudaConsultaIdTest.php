<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use Ramsey\Uuid\Exception\InvalidArgumentException as RamseyInvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use src\shared\domain\value_objects\AyudaConsultaId;
use Tests\myTest;

final class AyudaConsultaIdTest extends myTest
{
    private const VALID = '550e8400-e29b-41d4-a716-446655440002';

    public function test_create_valid(): void
    {
        $this->assertSame(self::VALID, AyudaConsultaId::fromString(self::VALID)->value());
    }

    public function test_invalid_throws_exception(): void
    {
        $this->expectException(RamseyInvalidArgumentException::class);
        new AyudaConsultaId('no-es-un-uuid');
    }

    public function test_to_string(): void
    {
        $this->assertSame(self::VALID, (string) new AyudaConsultaId(self::VALID));
    }

    public function test_random_es_un_uuid_distinto_cada_vez(): void
    {
        $a = AyudaConsultaId::random();
        $b = AyudaConsultaId::random();
        $this->assertTrue(RamseyUuid::isValid($a->value()));
        $this->assertFalse($a->equals($b));
    }

    public function test_fromNullableString_null(): void
    {
        $this->assertNull(AyudaConsultaId::fromNullableString(null));
        $this->assertNull(AyudaConsultaId::fromNullableString(''));
        $this->assertNull(AyudaConsultaId::fromNullableString('no-es-un-uuid'));
        $this->assertSame(self::VALID, AyudaConsultaId::fromNullableString(self::VALID)?->value());
    }
}
