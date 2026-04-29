<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\NullDateTimeLocal;
use Tests\myTest;

final class NullDateTimeLocalTest extends myTest
{
    public function test_format_returns_empty_override(): void
    {
        $n = new NullDateTimeLocal('now');
        $this->assertSame('', $n->format('Y-m-d'));
    }

    public function test_static_meses_contains_twelve_months(): void
    {
        $m = NullDateTimeLocal::Meses();
        $this->assertCount(12, $m);
        $this->assertArrayHasKey('1', $m);
    }

    public function test_get_fecha_latin_returns_empty(): void
    {
        $n = new NullDateTimeLocal('@0');
        $this->assertSame('', $n->getFechaLatin());
    }
}
