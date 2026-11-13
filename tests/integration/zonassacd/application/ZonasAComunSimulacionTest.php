<?php

declare(strict_types=1);

namespace Tests\integration\zonassacd\application;

use RuntimeException;
use src\zonassacd\application\ZonasAComun;
use Tests\myTest;

/**
 * Migración de zonassacd de sv-e a comun ({@see ZonasAComun}, `tools/fix/zonas_a_comun.php`) en modo
 * simulación, contra la BD de desarrollo: solo lee (aplicarla crea tablas, es DDL: necesita una BD
 * aislada, ver el backlog) e informa de cada esquema que aún tiene el módulo en sv-e. En la BD de
 * desarrollo la migración ya está hecha (ningún esquema de sv-e tiene `zonas`), así que aquí solo se
 * comprueba que la simulación no falla y que un esquema sin el módulo da el aviso.
 */
final class ZonasAComunSimulacionTest extends myTest
{
    public function test_simulacion_de_todos_los_esquemas_sin_errores(): void
    {
        $r = (new ZonasAComun())->execute(false);

        $this->assertFalse($r['aplicado']);
        $this->assertSame(0, $r['errores'], implode("\n", $r['lineas']));
        foreach ($r['lineas'] as $linea) {
            $this->assertMatchesRegularExpression('/^  \S+\s+sve zonas=\d+ grupos=\d+ sacd=\d+ ctr\(sv=\d+ sf=\d+\) comun zonas=\d+ grupos=\d+ sacd=\d+ ctr=\d+/', $linea);
            $this->assertStringNotContainsString('copiado', $linea, 'la simulación no copia');
        }
    }

    public function test_un_esquema_sin_el_modulo_avisa(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no tiene el módulo zonassacd instalado en sv-e');
        (new ZonasAComun())->execute(false, 'H-dlbv');
    }

    public function test_nombre_del_esquema_de_comun(): void
    {
        $this->assertSame('H-dlb', ZonasAComun::esquemaComunDeSve('H-dlbv'));
        $this->assertSame('H-dlb', ZonasAComun::esquemaComunDeSve('H-dlb'));
    }
}
