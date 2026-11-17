<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\entity\ActividadAll;
use src\casas\application\services\CasasResumenOcupacion;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/** Cálculo de ocupación de una casa por periodos sv/sf. */
final class CasasResumenOcupacionTest extends TestCase
{
    /** El periodo empieza a las 20:00 del día anterior y acaba a las 10:00 del día siguiente, sea la hora que sea. */
    public function test_los_limites_del_periodo_son_las_20_del_dia_anterior_y_las_10_del_siguiente(): void
    {
        $this->assertSame('2026-02-09 20:00:00', CasasResumenOcupacion::inicioPeriodo('20260210'));
        $this->assertSame('2026-02-11 10:00:00', CasasResumenOcupacion::finPeriodo('20260210'));
        $this->assertSame('2026-02-10 00:00:00', CasasResumenOcupacion::inicioPeriodo('20260210', 0));
    }

    public function test_una_fecha_invalida_da_cadena_vacia(): void
    {
        $this->assertSame('', CasasResumenOcupacion::inicioPeriodo('no es fecha'));
        $this->assertSame('', CasasResumenOcupacion::finPeriodo('2026-02-10'));
    }

    public function test_el_reparto_suma_los_dias_de_cada_periodo_a_su_seccion_e_ignora_fechas_invalidas(): void
    {
        $r = CasasResumenOcupacion::reparto([
            ['iso_ini' => '20260101', 'iso_fin' => '20260110', 'sfsv' => 1],
            ['iso_ini' => '20260111', 'iso_fin' => '20260120', 'sfsv' => '2'],
            ['iso_ini' => '20260121', 'iso_fin' => '20260125', 'sfsv' => 1],
            ['iso_ini' => 'xx', 'iso_fin' => '20260125', 'sfsv' => 1],
            ['iso_ini' => '20260201', 'iso_fin' => '20260203', 'sfsv' => 3],
        ]);

        $this->assertSame([1 => 13.0, 2 => 9.0, 3 => 2.0], $r);
    }

    public function test_sin_periodos_todo_es_cero(): void
    {
        $this->assertSame([1 => 0.0, 2 => 0.0, 3 => 0.0], CasasResumenOcupacion::reparto([]));
    }

    private function actividad(?string $ini, ?string $fin, ?string $hIni = null, ?string $hFin = null): ActividadAll
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getF_ini')->willReturn($ini === null ? null : new DateTimeLocal($ini));
        $a->method('getF_fin')->willReturn($fin === null ? null : new DateTimeLocal($fin));
        $a->method('getH_ini')->willReturn($hIni === null ? null : TimeLocal::fromString($hIni));
        $a->method('getH_fin')->willReturn($hFin === null ? null : TimeLocal::fromString($hFin));
        $a->method('getNom_activ')->willReturn('ca');
        $a->method('getDuracionReal')->willReturn(7.5);

        return $a;
    }

    private const PERIODOS = [
        ['iso_ini' => '20260101', 'iso_fin' => '20260630', 'sfsv' => 1],
        ['iso_ini' => '20260701', 'iso_fin' => '20261231', 'sfsv' => 2],
    ];

    public function test_una_actividad_sin_fechas_no_ocupa_nada(): void
    {
        $r = CasasResumenOcupacion::diasOcupacion(self::PERIODOS, $this->actividad(null, null), new DateTimeLocal('2026-01-01'), new DateTimeLocal('2026-12-31'));

        $this->assertSame([1 => 0.0, 2 => 0.0, 'avisos' => []], $r);
    }

    /** Una actividad de febrero cae en el primer periodo (sv, enero–junio). */
    public function test_una_actividad_de_febrero_se_atribuye_al_periodo_sv(): void
    {
        $r = CasasResumenOcupacion::diasOcupacion(self::PERIODOS, $this->actividad('2026-02-10', '2026-02-17'), new DateTimeLocal('2026-01-01'), new DateTimeLocal('2026-12-31'));

        $this->assertSame(7.5, $r[1]);
        $this->assertSame(0.0, $r[2]);
        $this->assertSame([], $r['avisos']);
    }
}
