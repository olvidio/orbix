<?php

declare(strict_types=1);

namespace Tests\unit\planning\support;

use frontend\planning\support\PlanningActivitySlots;
use PHPUnit\Framework\TestCase;

final class PlanningActivitySlotsTest extends TestCase
{
    private int $numSecIni0;

    protected function setUp(): void
    {
        $this->numSecIni0 = (int) mktime(0, 0, 0, 9, 1, 2026);
    }

    public function test_fin_a_las_10_y_inicio_a_las_20_mismo_dia_no_comparten_franja(): void
    {
        $fin = $this->indices(1, 16, 1, 9, 2026, 10, 0, 14, 9, 2026);
        $inicio = $this->indices(20, 0, 14, 9, 2026, 22, 0, 16, 9, 2026);

        $this->assertSame(40, $fin['n_dfi']);
        $this->assertSame(42, $inicio['n_dini']);
        $this->assertTrue($fin['n_dfi'] < $inicio['n_dini']);
    }

    public function test_fin_a_las_11_y_inicio_a_las_20_mismo_dia_no_comparten_franja(): void
    {
        $fin = $this->indices(11, 0, 14, 9, 2026, 11, 0, 14, 9, 2026);
        $inicio = $this->indices(20, 0, 14, 9, 2026, 22, 0, 14, 9, 2026);

        $this->assertSame(41, $fin['n_dfi']);
        $this->assertSame(42, $inicio['n_dini']);
        $this->assertTrue($fin['n_dfi'] < $inicio['n_dini']);
    }

    public function test_las_20_del_mismo_dia_caen_en_franja_tarde_aunque_round_antiguo_subiera_el_dia(): void
    {
        $slots = $this->indices(20, 0, 14, 9, 2026, 23, 0, 14, 9, 2026);

        $this->assertSame(42, $slots['n_dini']);
        $this->assertSame(42, $slots['n_dfi']);
    }

    /**
     * @return array{n_dini: int, n_dfi: int}
     */
    private function indices(
        int $hIni,
        int $mIni,
        int $dIni,
        int $mMesIni,
        int $yIni,
        int $hFi,
        int $mFi,
        int $dFi,
        int $mMesFi,
        int $yFi,
    ): array {
        return PlanningActivitySlots::indices(
            3,
            $this->numSecIni0,
            $dIni,
            $mMesIni,
            $yIni,
            $hIni,
            $mIni,
            0,
            $dFi,
            $mMesFi,
            $yFi,
            $hFi,
            $mFi,
            0,
        );
    }
}
