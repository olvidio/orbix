<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\TimeLocal;
use Tests\myTest;

final class TimeLocalTest extends myTest
{
    public function test_from_string_with_seconds(): void
    {
        $t = TimeLocal::fromString('14:30:45');
        $this->assertSame('14:30:45', $t->toDatabaseString());
    }

    public function test_from_string_without_seconds(): void
    {
        $t = TimeLocal::fromString('09:15');
        $this->assertSame('09:15:00', $t->toDatabaseString());
    }

    public function test_invalid_format_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TimeLocal::fromString('not-a-time');
    }

    public function test_is_before(): void
    {
        $early = TimeLocal::fromString('08:00:00');
        $late = TimeLocal::fromString('09:00:00');
        $this->assertTrue($early->isBefore($late));
        $this->assertFalse($late->isBefore($early));
    }
}
