<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\persistence\postgresql;

use PHPUnit\Framework\TestCase;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\persistence\postgresql\PgTimestamp;

final class PgTimestampTest extends TestCase
{
    public function test_toPg_devuelve_null_con_fecha_nula(): void
    {
        $converter = new PgTimestamp(null);

        $this->assertNull($converter->toPg('date'));
        $this->assertNull($converter->toPgStandardFormat());
    }

    public function test_toPg_devuelve_null_con_hora_nula(): void
    {
        $converter = new PgTimestamp(null);

        $this->assertNull($converter->toPg('time'));
    }

    public function test_toPg_formatea_fecha_real(): void
    {
        $converter = new PgTimestamp(new DateTimeLocal('2024-03-15'));

        $this->assertSame('2024-03-15', $converter->toPg('date'));
    }
}
