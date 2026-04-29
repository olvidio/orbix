<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

final class DateTimeLocalTest extends myTest
{
    public function test_create_from_format_and_iso(): void
    {
        $dt = DateTimeLocal::createFromFormat('Y-m-d', '2023-06-15');
        $this->assertInstanceOf(DateTimeLocal::class, $dt);
        $this->assertSame('2023-06-15', $dt->getIso());
        $this->assertStringStartsWith('2023-06-15', $dt->getIsoTime());
    }

    public function test_false_on_invalid_parse(): void
    {
        $dt = DateTimeLocal::createFromFormat('Y-m-d', 'invalid');
        $this->assertFalse($dt);
    }

    public function test_static_meses_latin_has_twelve_keys(): void
    {
        $latin = DateTimeLocal::Meses_latin();
        $this->assertSame('martio', $latin['3']);
        $this->assertCount(12, $latin);
    }

    public function test_duration_between_two_dates(): void
    {
        $start = DateTimeLocal::createFromFormat('Y-m-d', '2024-01-01');
        $this->assertInstanceOf(DateTimeLocal::class, $start);
        $other = DateTimeLocal::createFromFormat('Y-m-d', '2024-01-03');
        $this->assertInstanceOf(DateTimeLocal::class, $other);
        $dur = $start->duracion($other);
        $this->assertGreaterThanOrEqual(1.9, $dur);
    }
}
