<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure\persistence;

use PDO;
use Tests\myTest;

/**
 * Las conexiones a PostgreSQL que abre `DBConnection::getPDO()` (todas las `oDB*` del bootstrap) son
 * `Pdo\Pgsql`: los métodos propios del driver (`copyFromFile`, …) solo existen ahí; llamados sobre un
 * `PDO` genérico (`pgsqlCopyFromFile`, …) están obsoletos desde PHP 8.5.
 */
final class DBConnectionPdoPgsqlTest extends myTest
{
    public function test_las_conexiones_postgresql_son_pdo_pgsql(): void
    {
        $clases = [];
        foreach ($GLOBALS as $nombre => $valor) {
            if (!is_string($nombre) || !str_starts_with($nombre, 'oDB') || !$valor instanceof PDO
                || $valor->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
                continue;
            }
            $clases[$nombre] = $valor::class;
        }

        $this->assertNotSame([], $clases, 'la suite abre conexiones a PostgreSQL');
        $this->assertSame(
            [],
            array_filter($clases, static fn (string $clase): bool => $clase !== \Pdo\Pgsql::class),
            'conexiones que no son Pdo\Pgsql: ' . json_encode($clases),
        );
    }
}
