<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\SfsvOtrosId;
use Tests\myTest;

/**
 * Cobertura de {@see SfsvOtrosId} (nombre de fichero alineado con la clase VO).
 *
 * Los casos antes vivían en `SfsvIdTest.php`; se consolidaron aquí.
 */
final class SfsvOtrosIdTest extends myTest
{
    public function test_create_valid_sv(): void
    {
        $sfsvId = new SfsvOtrosId(SfsvOtrosId::SV);
        $this->assertSame(1, $sfsvId->value());
    }

    public function test_invalid_throws_invalid_argument_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new SfsvOtrosId(999);
    }

    public function test_equals(): void
    {
        $a = new SfsvOtrosId(SfsvOtrosId::SV);
        $b = new SfsvOtrosId(SfsvOtrosId::SV);
        $this->assertTrue($a->equals($b));

        $c = new SfsvOtrosId(SfsvOtrosId::SF);
        $this->assertFalse($a->equals($c));
    }

    public function test_from_nullable_int(): void
    {
        $this->assertNull(SfsvOtrosId::fromNullableInt(null));
        $this->assertInstanceOf(SfsvOtrosId::class, SfsvOtrosId::fromNullableInt(SfsvOtrosId::Otros));
    }
}
