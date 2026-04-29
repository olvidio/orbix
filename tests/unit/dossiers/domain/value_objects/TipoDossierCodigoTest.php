<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierCodigo;
use Tests\myTest;

final class TipoDossierCodigoTest extends myTest
{
    public function test_create_valid_trimmed(): void
    {
        $c = new TipoDossierCodigo('  abc  ');
        $this->assertSame('abc', $c->value());
    }

    public function test_too_long_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoDossierCodigo(str_repeat('x', 81));
    }

    public function test_from_nullable_string(): void
    {
        $this->assertNull(TipoDossierCodigo::fromNullableString(null));
        $this->assertNull(TipoDossierCodigo::fromNullableString('   '));
        $this->assertSame('ok', TipoDossierCodigo::fromNullableString('ok')?->value());
    }
}
