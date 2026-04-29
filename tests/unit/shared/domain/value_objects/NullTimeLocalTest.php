<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use ReflectionClass;
use src\shared\domain\value_objects\NullTimeLocal;
use Tests\myTest;

/**
 * La clase VO tiene varios stubs (p. ej. {@see NullTimeLocal::fromString} no devuelve instancia válida).
 * Las pruebas cubren comportamiento observable sin ejecutar ese método.
 */
final class NullTimeLocalTest extends myTest
{
    public function test_to_database_and_format_without_initialized_time_via_reflection(): void
    {
        $ref = new ReflectionClass(NullTimeLocal::class);
        /** @var NullTimeLocal $n */
        $n = $ref->newInstanceWithoutConstructor();

        $this->assertSame('', $n->toDatabaseString());
        $this->assertSame('', $n->format('H:i:s'));
    }
}
