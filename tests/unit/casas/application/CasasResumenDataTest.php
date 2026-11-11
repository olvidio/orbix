<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\casas\application\CasasResumenData;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;
use src\casas\domain\entity\Ingreso;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroEllas;
use Tests\unit\planning\support\DescribeCriteria;

/** Resumen económico de casas: ocupación, asistencias, ingresos, gastos y superávit por sección; por periodo o por años. */
final class CasasResumenDataTest extends TestCase
{
    private const PERIODO = ['periodo' => 'otro', 'empiezamin' => '1/1/2026', 'empiezamax' => '31/12/2026'];
    /** Un solo periodo de sv que abarca todo el año (y cruza los cambios de año). */
    private const TODO_SV = [['iso_ini' => '20251231', 'iso_fin' => '20270101', 'sfsv' => 1]];

    private CasaDlRepositoryInterface&MockObject $casas;
    private CentroEllasRepositoryInterface&MockObject $centrosSf;
    private ActividadRepositoryInterface&MockObject $actividades;
    private GrupoCasaRepositoryInterface&MockObject $grupos;
    private CasaPeriodoRepositoryInterface&MockObject $periodos;
    private IngresoRepositoryInterface&MockObject $ingresos;
    private UbiGastoRepositoryInterface&MockObject $gastos;

    /** @var array<string, mixed> */
    private array $sesionAnterior = [];
    /** @var list<string> */
    private array $filtrosCasas = [];
    /** @var list<Casa|CentroEllas> */
    private array $casasDevueltas = [];
    /** @var list<list<string>> */
    private array $filtrosActividades = [];
    /** @var array<int, list<ActividadAll>> */
    private array $actividadesPorCasa = [];
    /** @var array<int, list<array{iso_ini: string, iso_fin: string, sfsv: int}>> */
    private array $periodosPorCasa = [];
    /** @var array<int, Ingreso> */
    private array $ingresosPorActividad = [];
    /** @var array<int, array<int, float>> gastos por casa y tipo (1 aportación sv, 2 aportación sf, 3 gasto total) */
    private array $gastosPorCasa = [];
    /** @var list<GrupoCasa> */
    private array $gruposDevueltos = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION ?? [];
        // Solo hace falta el formato de fecha local (d/m/a) para `Periodo`.
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF-8';

        $this->casas = $this->createMock(CasaDlRepositoryInterface::class);
        $this->casas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosCasas = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $this->casasDevueltas;
        });
        $this->centrosSf = $this->createMock(CentroEllasRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            $this->filtrosActividades[] = array_merge($f, DescribeCriteria::orden($c));

            return $this->actividadesPorCasa[(int) substr($f[2], strlen('id_ubi eq '))] ?? [];
        });
        $this->grupos = $this->createMock(GrupoCasaRepositoryInterface::class);
        $this->grupos->method('matching')->willReturnCallback(function (Criteria $c): array {
            [$campo, , $valor] = explode(' ', DescribeCriteria::filtros($c)[0]);

            return array_values(array_filter(
                $this->gruposDevueltos,
                static fn (GrupoCasa $g): bool => ($campo === 'id_ubi_padre' ? $g->getId_ubi_padre() : $g->getId_ubi_hijo()) === (int) $valor
            ));
        });
        $this->periodos = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $this->periodos->method('getArrayCasaPeriodos')->willReturnCallback(
            fn (int $idUbi, DateTimeLocal $ini, DateTimeLocal $fin): array => $this->periodosPorCasa[$idUbi] ?? []
        );
        $this->ingresos = $this->createMock(IngresoRepositoryInterface::class);
        $this->ingresos->method('findById')->willReturnCallback(fn (int $id): ?Ingreso => $this->ingresosPorActividad[$id] ?? null);
        $this->gastos = $this->createMock(UbiGastoRepositoryInterface::class);
        $this->gastos->method('getSumaGastos')->willReturnCallback(
            fn (int $idUbi, int $tipo, DateTimeLocal $ini, DateTimeLocal $fin): float => $this->gastosPorCasa[$idUbi][$tipo] ?? 0.0
        );
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesionAnterior;
        parent::tearDown();
    }

    private function uc(): CasasResumenData
    {
        return new CasasResumenData(
            $this->casas,
            $this->centrosSf,
            $this->actividades,
            $this->grupos,
            $this->periodos,
            $this->ingresos,
            $this->gastos,
        );
    }

    private function casa(int $id, string $nombre): Casa&MockObject
    {
        $c = $this->createMock(Casa::class);
        $c->method('getId_ubi')->willReturn($id);
        $c->method('getNombre_ubi')->willReturn($nombre);

        return $c;
    }

    private function actividad(int $id, string $nombre, float $real = 5.0): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getNom_activ')->willReturn($nombre);
        $a->method('getF_ini')->willReturn(new DateTimeLocal('2026-02-10'));
        $a->method('getF_fin')->willReturn(new DateTimeLocal('2026-02-15'));
        $a->method('getH_ini')->willReturn(null);
        $a->method('getH_fin')->willReturn(null);
        $a->method('getDuracionReal')->willReturn($real);
        $a->method('getDuracionAumentada')->willReturn($real * 1.1);
        $a->method('getDuracionEnPeriodo')->willReturn($real);

        return $a;
    }

    private function ingreso(int $idActiv, ?int $previstos, ?int $asistentes, ?float $ingresosPrevistos, ?float $ingresos): void
    {
        $i = new Ingreso();
        $i->setId_activ($idActiv);
        $i->setNumAsistentesPrevistosVo($previstos);
        $i->setNumAsistentesVo($asistentes);
        $i->setIngresosPrevistosVo($ingresosPrevistos);
        $i->setIngresosVo($ingresos);
        $this->ingresosPorActividad[$idActiv] = $i;
    }

    private function grupo(int $padre, int $hijo): void
    {
        $g = new GrupoCasa();
        $g->setId_ubi_padre($padre);
        $g->setId_ubi_hijo($hijo);
        $this->gruposDevueltos[] = $g;
    }

    /**
     * @dataProvider seleccionProvider
     *
     * @param list<string> $filtros
     */
    public function test_cada_opcion_del_desplegable_selecciona_sus_casas(mixed $sel, array $filtros): void
    {
        $this->uc()->execute(['cdc_sel' => $sel, 'id_cdc' => ['77', 78]] + self::PERIODO);

        $this->assertSame(array_merge($filtros, ['nombre_ubi asc']), $this->filtrosCasas);
    }

    /**
     * @return iterable<string, array{mixed, list<string>}>
     */
    public static function seleccionProvider(): iterable
    {
        yield '1: comunes sv y sf' => [1, ['sv eq true', 'sf eq true']];
        yield '2: solo sf' => ['2', ['sv eq false', 'sf eq true']];
        yield '3: cdc/cdr de la dl comunes' => [3, ['sv eq true', 'sf eq true', 'tipo_ubi eq cdcdl', 'tipo_casa matches cdc|cdr']];
        yield '4: sv' => [4, ['sv eq true']];
        yield '5: sf' => [5, ['sf eq true']];
        yield '6: sf más centros de sf' => [6, ['sf eq true']];
        yield '9: las elegidas' => [9, ['id_ubi in [77,78]']];
        yield 'sin selección: todas' => ['', []];
        yield 'no numérico: todas' => ['x', []];
    }

    public function test_la_opcion_9_sin_casas_elegidas_las_trae_todas(): void
    {
        $this->uc()->execute(['cdc_sel' => 9] + self::PERIODO);

        $this->assertSame(['nombre_ubi asc'], $this->filtrosCasas);
    }

    public function test_la_opcion_6_anyade_los_centros_de_sf_que_son_casa(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta')];
        $ctr = $this->createMock(CentroEllas::class);
        $ctr->method('getId_ubi')->willReturn(2050);
        $ctr->method('getNombre_ubi')->willReturn('Ctr sf');
        $filtrosCtr = [];
        $this->centrosSf->expects($this->once())->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtrosCtr, $ctr): array {
            $filtrosCtr = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return [$ctr];
        });

        $r = $this->uc()->execute(['cdc_sel' => 6] + self::PERIODO);

        $this->assertSame(['cdc eq true', 'nombre_ubi asc'], $filtrosCtr);
        $this->assertSame([77, 2050], array_keys($r['a_resumen']));
        $this->assertSame('Ctr sf', $r['a_resumen'][2050][0]['nom']);
    }

    public function test_las_demas_opciones_no_consultan_los_centros_de_sf(): void
    {
        $this->centrosSf->expects($this->never())->method('matching');

        $this->uc()->execute(['cdc_sel' => 5] + self::PERIODO);
    }

    public function test_sin_casas_el_resumen_esta_vacio_y_los_totales_a_cero(): void
    {
        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame('periodo', $r['modo']);
        $this->assertSame([], $r['a_resumen']);
        $this->assertSame([], $r['avisos']);
        $this->assertSame(['gasto' => 0.0], $r['tot'][0]);
        $this->assertSame(0.0, $r['tot'][1]['dias']);
        $this->assertSame('-', $r['tot'][1]['dias%']);
        $this->assertSame('-', $r['tot'][2]['gasto%']);
    }

    public function test_las_actividades_son_las_que_tocan_el_periodo_en_esa_casa_sin_las_borrables(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta')];

        $this->uc()->execute(self::PERIODO);

        $this->assertSame(
            [['f_ini lte 2026-12-31', 'f_fin gte 2026-01-01', 'id_ubi eq 77', 'status lt 4', 'f_ini asc']],
            $this->filtrosActividades
        );
    }

    public function test_una_casa_sin_actividades_ni_periodos_queda_con_guiones(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta')];
        $this->gastosPorCasa[77] = [1 => 1000.0, 2 => 500.0, 3 => 4000.0];

        $fila = $this->uc()->execute(self::PERIODO)['a_resumen'][77];

        $this->assertSame(['nom' => 'Casa Alta', 'detalles' => [['vacio' => true]], 'gasto' => 4000.0], $fila[0]);
        $this->assertSame(0, $fila[1]['dias']);
        $this->assertSame('-', $fila[1]['dias%']);
        $this->assertSame(1000.0, $fila[1]['aportacion']);
        $this->assertSame(500.0, $fila[2]['aportacion']);
        // Sin periodos no se puede repartir el gasto entre secciones.
        $this->assertSame('-', $fila[1]['gasto%']);
        $this->assertSame(0, $fila[1]['gasto']);
        $this->assertSame(0, $fila[1]['superavit']);
        $this->assertSame([0, 0], [$fila['_id_ubi_padre'], $fila['_id_ubi_hijo']]);
    }

    public function test_fila_completa_de_una_casa_con_una_actividad_de_sv(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta')];
        $this->periodosPorCasa[77] = self::TODO_SV;
        $this->actividadesPorCasa[77] = [$this->actividad(3001, 'crt n')];
        $this->ingreso(3001, 30, 28, 3000.0, 2500.0);
        $this->gastosPorCasa[77] = [1 => 1000.0, 2 => 500.0, 3 => 4000.0];

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame([], $r['avisos']);
        $fila = $r['a_resumen'][77];
        // 5 días reales, aumentados un 10 % (medio día de margen) → 5,5 días de ocupación.
        $this->assertSame(
            [['nom_activ' => 'crt n', 'ocup_sv' => 5.5, 'ocup_sf' => 0.0, 'in_sv' => 2500.0, 'in_sf' => 0.0]],
            $fila[0]['detalles']
        );
        $this->assertSame([
            'dias' => 5.5, 'dias%' => '100',
            'asist_prev' => 165.0, 'asist_prev%' => '100',
            'asist' => 154.0, 'asist%' => '100',
            'in_prev_acu' => 3000.0, 'in_prev_acu%' => '100',
            'in_acu' => 2500.0, 'in_acu%' => '100',
            'gasto' => 4000.0, 'gasto%' => 100.0,
            'aportacion' => 1000.0,
            'superavit' => -500.0,
        ], $fila[1]);
        $this->assertSame(0.0, $fila[2]['dias']);
        $this->assertSame('0', $fila[2]['dias%']);
        $this->assertSame(0.0, $fila[2]['gasto']);
        $this->assertSame(0.0, $fila[2]['gasto%']);
        $this->assertSame(500.0, $fila[2]['superavit']);

        $tot = $r['tot'];
        $this->assertSame(4000.0, $tot[0]['gasto']);
        $this->assertSame(5.5, $tot[1]['dias']);
        $this->assertSame('100', $tot[1]['in_acu%']);
        $this->assertSame(-500.0, $tot[1]['superavit']);
        $this->assertSame(500.0, $tot[2]['superavit']);
    }

    public function test_el_gasto_se_reparte_entre_secciones_segun_los_dias_de_cada_periodo(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta')];
        $this->periodosPorCasa[77] = [
            ['iso_ini' => '20260101', 'iso_fin' => '20260131', 'sfsv' => 1],
            ['iso_ini' => '20260201', 'iso_fin' => '20260411', 'sfsv' => 2],
        ];
        $this->gastosPorCasa[77] = [1 => 0.0, 2 => 0.0, 3 => 4000.0];

        $fila = $this->uc()->execute(self::PERIODO)['a_resumen'][77];

        // 30 días de sv y 69 de sf.
        $this->assertSame([30.3, 69.7], [$fila[1]['gasto%'], $fila[2]['gasto%']]);
        $this->assertSame([1212.0, 2788.0], [$fila[1]['gasto'], $fila[2]['gasto']]);
        $this->assertSame([-1212.0, -2788.0], [$fila[1]['superavit'], $fila[2]['superavit']]);
    }

    public function test_una_actividad_sin_ingresos_previstos_ni_asistentes_se_avisa_y_no_suma_ingresos(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta')];
        $this->periodosPorCasa[77] = self::TODO_SV;
        $this->actividadesPorCasa[77] = [$this->actividad(3001, 'crt n'), $this->actividad(3002, 'ca n')];
        // La segunda tiene ingresos reales pero no previstos: los reales tampoco se cuentan.
        $this->ingreso(3002, 10, 10, null, 900.0);

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertCount(3, $r['avisos']);
        $this->assertStringContainsString('crt n', $r['avisos'][0]);
        $this->assertStringContainsString('crt n', $r['avisos'][1]);
        $this->assertStringContainsString('ca n', $r['avisos'][2]);
        $fila = $r['a_resumen'][77];
        $this->assertSame(11.0, $fila[1]['dias']);
        $this->assertSame(0.0, $fila[1]['in_acu']);
        $this->assertSame(55.0, $fila[1]['asist']);
        $this->assertCount(2, $fila[0]['detalles']);
    }

    public function test_las_actividades_sin_fechas_o_sin_duracion_se_saltan(): void
    {
        $sinFechas = $this->createMock(ActividadAll::class);
        $sinFechas->method('getF_ini')->willReturn(null);
        $sinFechas->method('getF_fin')->willReturn(null);
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta')];
        $this->actividadesPorCasa[77] = [$sinFechas, $this->actividad(3002, 'ca n', 0.0)];

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame([['vacio' => true]], $r['a_resumen'][77][0]['detalles']);
        $this->assertSame([], $r['avisos']);
    }

    public function test_el_superavit_de_una_casa_hija_se_suma_al_de_la_casa_madre(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta'), $this->casa(78, 'Anexo')];
        $this->grupo(77, 78);
        $this->periodosPorCasa[77] = self::TODO_SV;
        $this->periodosPorCasa[78] = self::TODO_SV;
        $this->actividadesPorCasa[77] = [$this->actividad(3001, 'crt n')];
        $this->actividadesPorCasa[78] = [$this->actividad(3002, 'ca n')];
        $this->ingreso(3001, 30, 28, 3000.0, 2500.0);
        $this->ingreso(3002, 10, 10, 1000.0, 800.0);
        $this->gastosPorCasa[77] = [1 => 1000.0, 2 => 500.0, 3 => 4000.0];
        $this->gastosPorCasa[78] = [1 => 0.0, 2 => 0.0, 3 => 600.0];

        $r = $this->uc()->execute(self::PERIODO);

        $madre = $r['a_resumen'][77];
        $hija = $r['a_resumen'][78];
        $this->assertSame([77, 78], [$hija['_id_ubi_padre'], $hija['_id_ubi_hijo']]);
        // Madre: aportación 1000 + ingresos propios 2500 + ingresos de la hija 800 − gasto 4000.
        $this->assertSame(300.0, $madre[1]['superavit']);
        $this->assertSame(500.0, $madre[2]['superavit']);
        $this->assertSame(['', ''], [$hija[1]['superavit'], $hija[2]['superavit']]);
        // El total solo cuenta el de la madre (el de la hija está vacío).
        $this->assertSame(300.0, $r['tot'][1]['superavit']);
        $this->assertSame(4600.0, $r['tot'][0]['gasto']);
    }

    /**
     * Hallazgo: el gasto propio de la casa hija (600) no entra en el superávit conjunto: se suman sus ingresos a
     * los de la madre pero solo se resta el gasto de la madre. Lo fija el test anterior (300 y no −300).
     * Además, si la madre no tiene periodos (reparto «-») su superávit no se toca, pero el de la hija se vacía.
     */
    public function test_si_la_madre_no_tiene_periodos_su_superavit_no_cambia_pero_el_de_la_hija_se_vacia(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta'), $this->casa(78, 'Anexo')];
        $this->grupo(77, 78);
        $this->periodosPorCasa[78] = self::TODO_SV;
        $this->gastosPorCasa[78] = [1 => 200.0, 2 => 0.0, 3 => 600.0];

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame(0, $r['a_resumen'][77][1]['superavit']);
        $this->assertSame('', $r['a_resumen'][78][1]['superavit']);
    }

    public function test_si_la_casa_madre_no_esta_en_el_listado_la_hija_conserva_su_superavit(): void
    {
        $this->casasDevueltas = [$this->casa(78, 'Anexo')];
        $this->grupo(77, 78);
        $this->periodosPorCasa[78] = self::TODO_SV;
        $this->gastosPorCasa[78] = [1 => 200.0, 2 => 0.0, 3 => 600.0];

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame(-400.0, $r['a_resumen'][78][1]['superavit']);
    }

    public function test_los_totales_suman_las_casas_y_reparten_los_porcentajes_entre_secciones(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta'), $this->casa(79, 'Casa Nova')];
        $this->periodosPorCasa[77] = self::TODO_SV;
        $this->periodosPorCasa[79] = [['iso_ini' => '20251231', 'iso_fin' => '20270101', 'sfsv' => 2]];
        $this->actividadesPorCasa[77] = [$this->actividad(3001, 'crt n')];
        $this->actividadesPorCasa[79] = [$this->actividad(3003, 'crt sf')];
        $this->ingreso(3001, 30, 28, 3000.0, 2500.0);
        $this->ingreso(3003, 10, 10, 1000.0, 7500.0);
        $this->gastosPorCasa[77] = [1 => 1000.0, 2 => 0.0, 3 => 4000.0];
        $this->gastosPorCasa[79] = [1 => 0.0, 2 => 0.0, 3 => 6000.0];

        $tot = $this->uc()->execute(self::PERIODO)['tot'];

        $this->assertSame(10000.0, $tot[0]['gasto']);
        $this->assertSame([5.5, 5.5], [$tot[1]['dias'], $tot[2]['dias']]);
        $this->assertSame(['50', '50'], [$tot[1]['dias%'], $tot[2]['dias%']]);
        $this->assertSame([2500.0, 7500.0], [$tot[1]['in_acu'], $tot[2]['in_acu']]);
        $this->assertSame(['25', '75'], [$tot[1]['in_acu%'], $tot[2]['in_acu%']]);
        $this->assertSame([4000.0, 6000.0], [$tot[1]['gasto'], $tot[2]['gasto']]);
        $this->assertSame(['40', '60'], [$tot[1]['gasto%'], $tot[2]['gasto%']]);
        $this->assertSame(1000.0, $tot[1]['aportacion']);
        $this->assertSame([-500.0, 1500.0], [$tot[1]['superavit'], $tot[2]['superavit']]);
    }

    public function test_el_modo_anual_da_seis_anyos_por_casa_desde_el_proximo_hacia_atras(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta'), $this->casa(78, 'Anexo')];
        $this->gastosPorCasa[77] = [1 => 0.0, 2 => 0.0, 3 => 4000.0];
        $prox = (int) date('Y') + 1;
        $anys = [$prox, $prox - 1, $prox - 2, $prox - 3, $prox - 4, $prox - 5];

        $r = $this->uc()->execute(['que' => 'anual', 'cdc_sel' => 4]);

        $this->assertSame('anual', $r['modo']);
        $this->assertSame($anys, $r['a_anys']);
        $this->assertSame([77, 78], array_keys($r['a_resumen']));
        $this->assertSame($anys, array_keys($r['a_resumen'][77]));
        $this->assertSame('Casa Alta', $r['a_resumen'][77][$prox][0]['nom']);
        $this->assertSame($anys, array_keys($r['tot']));
        $this->assertSame(4000.0, $r['tot'][$prox - 5][0]['gasto']);
        // Una consulta de actividades por casa y año, cada una con su año natural.
        $this->assertCount(12, $this->filtrosActividades);
        $this->assertSame(["f_ini lte $prox-12-31", "f_fin gte $prox-01-01", 'id_ubi eq 77', 'status lt 4', 'f_ini asc'], $this->filtrosActividades[0]);
        $this->assertSame('f_ini lte ' . ($prox - 5) . '-12-31', $this->filtrosActividades[11][0]);
    }

    public function test_en_el_modo_anual_el_superavit_madre_hija_se_aplica_en_cada_anyo(): void
    {
        $this->casasDevueltas = [$this->casa(77, 'Casa Alta'), $this->casa(78, 'Anexo')];
        $this->grupo(77, 78);
        $prox = (int) date('Y') + 1;
        $todoSv = [['iso_ini' => ($prox - 6) . '1231', 'iso_fin' => ($prox + 1) . '0101', 'sfsv' => 1]];
        $this->periodosPorCasa[77] = $todoSv;
        $this->periodosPorCasa[78] = $todoSv;
        $this->gastosPorCasa[77] = [1 => 1000.0, 2 => 0.0, 3 => 4000.0];
        $this->gastosPorCasa[78] = [1 => 0.0, 2 => 0.0, 3 => 600.0];

        $r = $this->uc()->execute(['que' => 'anual']);

        foreach ($r['a_anys'] as $any) {
            $this->assertSame(-3000.0, $r['a_resumen'][77][$any][1]['superavit']);
            $this->assertSame('', $r['a_resumen'][78][$any][1]['superavit']);
            $this->assertSame(-3000.0, $r['tot'][$any][1]['superavit']);
        }
    }
}
