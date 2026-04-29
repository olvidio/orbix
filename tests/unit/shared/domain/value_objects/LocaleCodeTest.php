<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\LocaleCode;
use Tests\myTest;

final class LocaleCodeTest extends myTest
{
    public function test_spanish_factory_and_value(): void
    {
        $l = LocaleCode::spanish();
        $this->assertSame('es_ES.UTF-8', $l->value());
    }

    public function test_normalize_trim_and_case(): void
    {
        $l = new LocaleCode('  es_es.utf-8 ');
        $this->assertSame('es_ES.UTF-8', $l->value());
    }

    public function test_invalid_format_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new LocaleCode('solo-texto-invalido');
    }

    public function test_empty_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new LocaleCode('');
    }

    public function test_from_nullable_string(): void
    {
        $this->assertNull(LocaleCode::fromNullableString(null));
        $this->assertNull(LocaleCode::fromNullableString('   '));
    }

    public function test_equals(): void
    {
        $a = new LocaleCode('ca_ES.UTF-8');
        $b = new LocaleCode('ca_ES.UTF-8');
        $this->assertTrue($a->equals($b));
    }
}
