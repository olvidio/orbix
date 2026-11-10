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
    /**
     * Hallazgo: `createFromFormat('Ymd', …)` sin `!` rellena la hora con la hora actual del reloj, así que los
     * límites del periodo («fecha − 4 h» y «fecha + 1 día + 10 h») dependen de la hora a la que se calcule: no son
     * 20:00 del día anterior ni 10:00 del siguiente, como pretende el código.
     */
    public function test_los_limites_del_periodo_arrastran_la_hora_actual_del_reloj(): void
    {
        $ini = CasasResumenOcupacion::inicioPeriodo('20260210');
        $fin = CasasResumenOcupacion::finPeriodo('20260210');

        $this->assertMatchesRegularExpression('/^2026-02-(09|10) \d\d:\d\d:\d\d$/', $ini);
        $this->assertMatchesRegularExpression('/^2026-02-1[12] \d\d:\d\d:\d\d$/', $fin);
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

    /**
     * Hallazgo: `inicioPeriodo`/`finPeriodo` devuelven `Y-m-d H:i:s` y la actividad se compara en formato
     * `YmdHis`; al comparar cadenas de formatos distintos (`2026…` frente a `2026-…`) una actividad de febrero,
     * que cae en el primer periodo (sv, enero–junio), se atribuye al segundo (sf, julio–diciembre).
     */
    public function test_una_actividad_de_febrero_en_el_periodo_sv_se_atribuye_por_error_a_sf(): void
    {
        $r = CasasResumenOcupacion::diasOcupacion(self::PERIODOS, $this->actividad('2026-02-10', '2026-02-17'), new DateTimeLocal('2026-01-01'), new DateTimeLocal('2026-12-31'));

        $this->assertSame(0.0, $r[1]);
        $this->assertSame(7.5, $r[2], 'debería ser sv (sección 1)');
        $this->assertSame([], $r['avisos']);
    }
}
