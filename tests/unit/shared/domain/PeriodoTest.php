<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain;

use frontend\shared\web\Periodo as PeriodoFrontend;
use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\domain\Periodo;
use Tests\myTest;

/**
 * `src\shared\domain\Periodo`: alias de periodo → fechas de inicio y fin. Sustituye al `frontend\shared\web\Periodo`
 * que usaban las clases de `src/` (regla de capas); el resultado tiene que ser el mismo.
 */
final class PeriodoTest extends myTest
{
    private const CALENDARIO = [
        'mes_fin_stgr' => 6, 'mes_fin_crt' => 8,
        'dia_ini_stgr' => 1, 'mes_ini_stgr' => 10, 'dia_fin_stgr' => 30,
        'dia_ini_crt' => 1, 'mes_ini_crt' => 9, 'dia_fin_crt' => 31,
        'any_final_est' => 2027, 'any_final_crt' => 2027,
    ];

    /** @return iterable<string, array{string}> */
    public static function periodosProvider(): iterable
    {
        foreach (['otro', 'actual', 'desdeHoy', 'curso', 'curso_crt', 'curso_ca', 'navidad', 'trimestre', 'mes', 'verano',
                     'trimestre_1', 'trimestre_2', 'trimestre_3', 'trimestre_4', 'tot_any', 'any_prox', '', 'desconocido'] as $p) {
            yield $p === '' ? 'vacío' : $p => [$p];
        }
    }

    #[DataProvider('periodosProvider')]
    public function test_da_las_mismas_fechas_que_el_periodo_del_frontend(string $periodo): void
    {
        foreach (['', '2025'] as $any) {
            $nuevo = new Periodo(self::CALENDARIO);
            $antiguo = new PeriodoFrontend();
            $antiguo->setCalendarioEscolar(self::CALENDARIO);
            foreach ([$nuevo, $antiguo] as $o) {
                $o->setDefaultAny('next');
                $o->setAny($any);
                $o->setEmpiezaMin('3/2/2026');
                $o->setEmpiezaMax('20/4/2026');
                $o->setPeriodo($periodo);
            }

            $this->assertSame([$antiguo->getF_ini_iso(), $antiguo->getF_fin_iso()], [$nuevo->getF_ini_iso(), $nuevo->getF_fin_iso()], "año '$any'");
        }
    }

    public function test_fechas_concretas(): void
    {
        $p = new Periodo();
        $p->setAny(2025);
        $p->setPeriodo('trimestre_2');
        $this->assertSame(['2025/4/1', '2025/6/30'], [$p->getF_ini_iso(), $p->getF_fin_iso()]);
        $this->assertSame('1/4/2025 - 30/6/2025', $p->getTxt_cusro());
        $this->assertSame('2025-06-30', $p->getF_fin()->getIso());

        $p->setPeriodo('any_prox');
        $this->assertSame(['2026/1/1', '2026/12/31'], [$p->getF_ini_iso(), $p->getF_fin_iso()]);

        $p->setEmpiezaMin('3/2/2026');
        $p->setEmpiezaMax('no es fecha');
        $p->setPeriodo('otro');
        $this->assertSame(['2026-02-03', ''], [$p->getF_ini_iso(), $p->getF_fin_iso()]);
    }

    public function test_por_defecto_el_anyo_es_el_actual_y_setAny_ignora_vacio_y_cero(): void
    {
        $p = new Periodo();
        $p->setDefaultAny('prev');
        $p->setAny('');
        $p->setAny(0);
        $p->setPeriodo('tot_any');

        $this->assertSame(((int) date('Y') - 1) . '/1/1', $p->getF_ini_iso());
    }

    public function test_los_periodos_de_curso_sin_calendario_escolar_fallan(): void
    {
        $this->expectException(\RuntimeException::class);
        (new Periodo())->setPeriodo('curso');
    }

    public function test_sin_periodo_calculado_no_hay_fecha(): void
    {
        $this->expectException(\RuntimeException::class);
        (new Periodo())->getF_ini();
    }
}
