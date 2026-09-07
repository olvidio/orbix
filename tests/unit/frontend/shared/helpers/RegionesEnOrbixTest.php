<?php

declare(strict_types=1);

namespace Tests\unit\frontend\shared\helpers;

use frontend\shared\helpers\RegionesEnOrbix;
use PHPUnit\Framework\TestCase;

final class RegionesEnOrbixTest extends TestCase
{
    public function test_codigo_todos_recorre_todas_las_filas_en_orden(): void
    {
        $codigos = [];
        foreach (RegionesEnOrbix::bloques() as $filas) {
            foreach ($filas as $fila) {
                $codigos[] = $fila['codigo'];
            }
        }

        $this->assertNotSame([], $codigos);
        $this->assertSame(implode(',', $codigos), RegionesEnOrbix::codigoTodos());
        $this->assertSame(
            array_merge(
                array_column(RegionesEnOrbix::regiones(), 'codigo'),
                array_column(RegionesEnOrbix::delegaciones(), 'codigo'),
            ),
            $codigos,
        );
    }
}
