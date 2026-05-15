<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\domain\value_objects;

use InvalidArgumentException;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use Tests\myTest;

final class MigracionDatabaseTest extends myTest
{
    public function test_valores_validos(): void
    {
        foreach (MigracionDatabase::validValues() as $value) {
            $this->assertSame($value, (new MigracionDatabase($value))->value());
        }
    }

    public function test_valor_invalido_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MigracionDatabase('otra');
    }
}
