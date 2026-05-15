<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\domain\value_objects;

use InvalidArgumentException;
use src\devel_db_admin\domain\value_objects\MigracionTipo;
use Tests\myTest;

final class MigracionTipoTest extends myTest
{
    public function test_valores_validos(): void
    {
        foreach (MigracionTipo::validValues() as $value) {
            $this->assertSame($value, (new MigracionTipo($value))->value());
        }
    }

    public function test_valor_invalido_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MigracionTipo('otro');
    }
}
