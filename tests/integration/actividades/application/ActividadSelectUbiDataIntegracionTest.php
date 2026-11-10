<?php

declare(strict_types=1);

namespace Tests\integration\actividades\application;

use src\actividades\application\ActividadSelectUbiData;
use Tests\myTest;

/** Desplegables de «seleccionar lugar para una actividad» sobre la BD real (solo lectura). */
final class ActividadSelectUbiDataIntegracionTest extends myTest
{
    public function test_sin_dl_organizadora_no_hay_lugares_frecuentes_pero_si_regiones(): void
    {
        $r = (new ActividadSelectUbiData())->execute([]);

        $this->assertSame([], $r['opcionesFreq']);
        $this->assertNotSame([], $r['opcionesRegion']);
        foreach ($r['opcionesRegion'] as [$valor, $etiqueta]) {
            $this->assertMatchesRegularExpression('/^(dl|r)\|/', $valor);
            $this->assertIsString($etiqueta);
        }
    }

    public function test_las_dl_cr_no_salen_como_delegaciones_y_el_orden_es_por_etiqueta(): void
    {
        $r = (new ActividadSelectUbiData())->execute([]);

        $dls = array_filter(array_column($r['opcionesRegion'], 0), static fn (string $v): bool => str_starts_with($v, 'dl|'));
        foreach ($dls as $v) {
            $this->assertDoesNotMatchRegularExpression('/^dl\|cr/', $v);
        }
        $etiquetas = array_column($r['opcionesRegion'], 1);
        $ordenadas = $etiquetas;
        usort($ordenadas, 'strcmp');
        $this->assertSame(count($etiquetas), count($ordenadas));
    }

    /**
     * @dataProvider sfsvProvider
     */
    public function test_los_lugares_frecuentes_se_consultan_para_cualquier_dl_y_filtro_sfsv(int $isfsv): void
    {
        $r = (new ActividadSelectUbiData())->execute(['dl_org' => 'dlb', 'isfsv' => $isfsv]);

        foreach ($r['opcionesFreq'] as [$id, $nombre]) {
            $this->assertMatchesRegularExpression('/^\d+$/', $id);
            $this->assertNotSame('', $nombre);
        }
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function sfsvProvider(): iterable
    {
        yield 'sin filtro' => [0];
        yield 'sv' => [1];
        yield 'sf' => [2];
    }

    public function test_una_dl_con_comillas_no_rompe_la_consulta(): void
    {
        $r = (new ActividadSelectUbiData())->execute(['dl_org' => "d'l\"; DROP TABLE x;--"]);

        $this->assertSame([], $r['opcionesFreq']);
    }
}
