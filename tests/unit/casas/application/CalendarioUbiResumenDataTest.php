<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\TiposActividades;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\casas\application\CalendarioUbiResumenData;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\entity\Ingreso;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\TarifaUbi;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Estudio económico de una casa: tarifas actuales y previstas, días por sección, gastos y previsión de ingresos. */
final class CalendarioUbiResumenDataTest extends myTest
{
    private const TIPO = 112025;

    private CasaDlRepositoryInterface&MockObject $casas;
    private TipoTarifaRepositoryInterface&MockObject $tiposTarifa;
    private TarifaUbiRepositoryInterface&MockObject $tarifasUbi;
    private CasaPeriodoRepositoryInterface&MockObject $periodos;
    private UbiGastoRepositoryInterface&MockObject $gastos;
    private ActividadRepositoryInterface&MockObject $actividades;
    private IngresoRepositoryInterface&MockObject $ingresos;

    private int $any;
    /** @var list<string> */
    private array $filtrosTipoTarifa = [];
    /** @var list<string> */
    private array $filtrosActividades = [];
    /** @var list<TipoTarifa> */
    private array $tiposDevueltos = [];
    /** @var array<string, TarifaUbi> clave "id_tarifa/año" */
    private array $tarifasPorAnyo = [];
    /** @var list<ActividadAll> */
    private array $actividadesDevueltas = [];
    /** @var array<int, float> suma de gastos por tipo (1 sv, 2 sf, 3 total) */
    private array $sumaGastos = [1 => 5000.0, 2 => 3000.0, 3 => 30000.0];
    /** @var array<int, int> días por sección */
    private array $dias = [1 => 100, 2 => 50];
    /** @var list<array{int, string, string}> */
    private array $llamadasGastos = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->any = (int) date('Y');

        $this->casas = $this->createMock(CasaDlRepositoryInterface::class);
        $this->casas->method('findById')->willReturnCallback(function (int $id): ?Casa {
            if ($id !== 77) {
                return null;
            }
            $c = $this->createMock(Casa::class);
            $c->method('getNombre_ubi')->willReturn('Casa Alta');
            $c->method('getPlazas_min')->willReturn(20);

            return $c;
        });
        $this->tiposTarifa = $this->createMock(TipoTarifaRepositoryInterface::class);
        $this->tiposTarifa->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosTipoTarifa = DescribeCriteria::filtros($c);

            return $this->tiposDevueltos;
        });
        $this->tarifasUbi = $this->createMock(TarifaUbiRepositoryInterface::class);
        $this->tarifasUbi->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            $clave = substr($f[0], strlen('id_tarifa eq ')) . '/' . substr($f[2], strlen('year eq '));

            return isset($this->tarifasPorAnyo[$clave]) ? [$this->tarifasPorAnyo[$clave]] : [];
        });
        $this->periodos = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $this->periodos->method('getCasaPeriodosDias')->willReturnCallback(
            fn (int $seccion, int $idUbi, DateTimeLocal $ini, DateTimeLocal $fin): int => $this->dias[$seccion]
        );
        $this->gastos = $this->createMock(UbiGastoRepositoryInterface::class);
        $this->gastos->method('getSumaGastos')->willReturnCallback(
            function (int $idUbi, int $tipo, DateTimeLocal $ini, DateTimeLocal $fin): float {
                $this->llamadasGastos[] = [$tipo, $ini->getIso(), $fin->getIso()];

                return $this->sumaGastos[$tipo];
            }
        );
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosActividades = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $this->actividadesDevueltas;
        });
        $this->ingresos = $this->createMock(IngresoRepositoryInterface::class);
    }

    private function uc(): CalendarioUbiResumenData
    {
        return new CalendarioUbiResumenData(
            $this->casas,
            $this->tiposTarifa,
            $this->tarifasUbi,
            $this->periodos,
            $this->gastos,
            $this->actividades,
            $this->ingresos,
        );
    }

    private function tipoTarifa(int $id, int $modo, string $letra, ?float $actual, ?float $prevista, int $idItemPrev = 900): void
    {
        $t = $this->createMock(TipoTarifa::class);
        $t->method('getId_tarifa')->willReturn($id);
        $t->method('getModo')->willReturn($modo);
        $t->method('getLetra')->willReturn($letra);
        $this->tiposDevueltos[] = $t;
        if ($actual !== null) {
            $tu = $this->createMock(TarifaUbi::class);
            $tu->method('getCantidad')->willReturn($actual);
            $this->tarifasPorAnyo[$id . '/' . $this->any] = $tu;
        }
        if ($prevista !== null) {
            $tu = $this->createMock(TarifaUbi::class);
            $tu->method('getCantidad')->willReturn($prevista);
            $tu->method('getId_item')->willReturn($idItemPrev);
            $this->tarifasPorAnyo[$id . '/' . ($this->any + 1)] = $tu;
        }
    }

    private function actividad(int $id, float $enPeriodo = 5.0): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getId_tipo_activ')->willReturn(self::TIPO);
        $a->method('getF_ini')->willReturn(new DateTimeLocal(($this->any + 1) . '-02-10'));
        $a->method('getF_fin')->willReturn(new DateTimeLocal(($this->any + 1) . '-02-15'));
        $a->method('getDuracionAumentada')->willReturn(5.5);
        $a->method('getDuracionReal')->willReturn(5.0);
        $a->method('getDuracionEnPeriodo')->willReturn($enPeriodo);

        return $a;
    }

    private function asistentes(?int $n): void
    {
        if ($n === null) {
            $this->ingresos->method('findById')->willReturn(null);

            return;
        }
        $i = new Ingreso();
        $i->setId_activ(3001);
        $i->setNumAsistentesVo($n);
        $this->ingresos->method('findById')->willReturn($i);
    }

    public function test_una_casa_que_no_existe_devuelve_error_con_el_esqueleto_vacio(): void
    {
        $this->tiposTarifa->expects($this->never())->method('matching');

        $r = $this->uc()->execute(['id_ubi' => '999', 'seccion' => 'sv', 'G' => '10', 'inc_t' => '3']);

        $this->assertFalse($r['ok']);
        $this->assertNotSame('', $r['error']);
        $this->assertSame(999, $r['id_ubi']);
        $this->assertSame('sv', $r['seccion']);
        $this->assertSame([$this->any, $this->any - 1, $this->any + 1], [$r['any_actual'], $r['any_anterior'], $r['any_prev']]);
        $this->assertSame([10, 3], [$r['G'], $r['inc_t']]);
        $this->assertSame([], $r['a_actividades']);
    }

    /**
     * @dataProvider seccionProvider
     */
    public function test_la_seccion_decide_que_tarifas_y_que_actividades_se_miran(string $seccion, int $isfsv): void
    {
        $this->uc()->execute(['id_ubi' => 77, 'seccion' => $seccion]);

        $prev = $this->any + 1;
        $this->assertSame(['sfsv eq ' . $isfsv], $this->filtrosTipoTarifa);
        $this->assertSame([
            'id_ubi eq 77',
            "f_ini lte $prev-12-31",
            "f_fin gte $prev-01-01",
            'id_tipo_activ matches ^' . $isfsv,
            'f_ini asc',
        ], $this->filtrosActividades);
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function seccionProvider(): iterable
    {
        yield 'sv' => ['sv', 1];
        yield 'sf' => ['sf', 2];
        yield 'sin sección' => ['', 0];
    }

    public function test_los_gastos_son_los_del_anyo_anterior_y_los_dias_los_del_siguiente(): void
    {
        $prev = $this->any + 1;
        $this->periodos->expects($this->exactly(2))->method('getCasaPeriodosDias')->with(
            $this->anything(),
            77,
            $this->callback(static fn (DateTimeLocal $d): bool => $d->getIso() === "$prev-01-01"),
            $this->callback(static fn (DateTimeLocal $d): bool => $d->getIso() === "$prev-12-31"),
        );

        $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $ant = $this->any - 1;
        $this->assertSame([[1, "$ant-01-01", "$ant-12-31"], [2, "$ant-01-01", "$ant-12-31"], [3, "$ant-01-01", "$ant-12-31"]], $this->llamadasGastos);
    }

    /**
     * Hallazgo: la respuesta «sin gastos del año anterior» pretende incluir el nombre de la casa, las plazas, las
     * tarifas y los días, pero se monta con `empty() + [...]` y la unión de arrays conserva los valores de la
     * izquierda (los vacíos). Solo llegan `ok` y `error`. El frontend solo usa `id_ubi` y `any_anterior`.
     */
    public function test_sin_gastos_del_anyo_anterior_se_avisa_y_los_datos_ya_calculados_se_pierden(): void
    {
        $this->sumaGastos[3] = 0.0;
        $this->tipoTarifa(5, 2, 'A', 10.0, 12.0);
        $this->actividades->expects($this->never())->method('matching');

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $this->assertFalse($r['ok']);
        $this->assertSame('sin_gastos_anterior', $r['error']);
        $this->assertSame(77, $r['id_ubi']);
        $this->assertSame($this->any - 1, $r['any_anterior']);
        $this->assertSame('', $r['nombre_ubi'], 'debería ser «Casa Alta»');
        $this->assertSame(0, $r['plazas_min']);
        $this->assertSame([], $r['a_tarifas_actual']);
        $this->assertSame(0, $r['p_dv']);
    }

    public function test_estudio_completo_de_sv_con_tarifa_por_dia(): void
    {
        $this->tipoTarifa(5, 2, 'A', 10.0, 12.0);
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->asistentes(30);

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv', 'G' => 10]);

        $this->assertTrue($r['ok']);
        $this->assertSame('', $r['error']);
        $this->assertSame('Casa Alta', $r['nombre_ubi']);
        $this->assertSame(20, $r['plazas_min']);
        $this->assertSame([100, 50], [$r['p_dv'], $r['p_df']]);
        $this->assertSame([5 => ['letra' => 'A', 'modo' => 2, 'cantidad' => 10.0]], $r['a_tarifas_actual']);
        $this->assertSame([5 => ['id_item' => 900, 'modo' => 2, 'letra' => 'A', 'cantidad' => 12.0]], $r['a_tarifas_prev']);
        $this->assertSame(['id_ubi' => 77, 'id_items' => [900]], HashB::open($r['ctx_inc'], 'tarifa_ubi_update_inc'));

        // Gastos: total del año anterior y aportación de la sección.
        $this->assertSame(30000.0, $r['r_it']);
        $this->assertSame(5000.0, $r['r_idl']);
        // Previsión de ingresos: (1 + 10 %) × 30000 × 100 / 150
        $this->assertSame(22000.0, $r['p_ip']);
        $this->assertSame(2000, $r['p_ta_min']);
        $this->assertSame(100, $r['p_dseccion']);

        $tipo = new TiposActividades((string) self::TIPO);
        $prev = $this->any + 1;
        $this->assertSame([[
            'nom' => $tipo->getNom() . " (10/2/$prev - 15/2/$prev)",
            'dias' => 5.5,
            'asistentes' => 30,
            'asistencias' => 165.0,
            'id_tarifa' => 'A',
            'ingresos' => 1980.0,
        ]], $r['a_actividades']);
        $this->assertSame(1, $r['p_tac']);
        $this->assertSame(5.5, $r['p_tda']);
        $this->assertSame(30, $r['p_tap']);
        $this->assertSame(165.0, $r['p_ta']);
        $this->assertSame(1980.0, $r['p_tia']);
        $this->assertSame(1650.0, $r['r_tia']);
        $this->assertSame(12.0, $r['p_tarifa']);
        $this->assertSame(24000.0, $r['p_ti_min']);
        $this->assertSame(94.5, $r['dias_libres']);
        $this->assertSame(1835.0, $r['dif_asistencias']);
        $this->assertSame(22020.0, $r['dif_ingresos']);
        $this->assertSame(1668, $r['inc_p']);
        $this->assertSame(83.0, $r['inc_d']);
        $this->assertSame(1233, $r['inc_pt']);
    }

    public function test_en_sf_se_usan_los_dias_y_la_aportacion_de_sf(): void
    {
        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sf', 'G' => 10]);

        $this->assertSame(3000.0, $r['r_idl']);
        $this->assertSame(11000.0, $r['p_ip']);
        $this->assertSame(1000, $r['p_ta_min']);
        $this->assertSame(50, $r['p_dseccion']);
        $this->assertSame(50.0, $r['dias_libres']);
    }

    public function test_sin_seccion_no_hay_prevision_de_ingresos(): void
    {
        $r = $this->uc()->execute(['id_ubi' => 77]);

        $this->assertTrue($r['ok']);
        $this->assertSame(0.0, $r['p_ip']);
        $this->assertSame('', $r['p_ip_txt']);
        $this->assertSame(0, $r['dias_libres']);
    }

    public function test_sin_dias_asignados_la_prevision_es_cero_y_no_divide_por_cero(): void
    {
        $this->dias = [1 => 0, 2 => 0];

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv', 'G' => 10]);

        $this->assertSame(0.0, $r['p_ip']);
    }

    public function test_tarifa_por_actividad_modo_1_los_ingresos_son_asistentes_por_cantidad(): void
    {
        $this->tipoTarifa(5, 1, 'B', 100.0, 110.0);
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->asistentes(30);

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $this->assertSame(3300.0, $r['a_actividades'][0]['ingresos']);
        $this->assertSame(3000.0, $r['r_tia']);
    }

    public function test_sin_asistentes_anotados_se_cuentan_las_plazas_minimas_de_la_casa(): void
    {
        $this->tipoTarifa(5, 2, 'A', 10.0, 12.0);
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->asistentes(null);

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $this->assertSame(20, $r['a_actividades'][0]['asistentes']);
        $this->assertSame(110.0, $r['a_actividades'][0]['asistencias']);
    }

    public function test_sin_tarifa_prevista_se_usa_la_actual_y_no_hay_item_que_incrementar(): void
    {
        $this->tipoTarifa(5, 2, 'A', 10.0, null);

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $this->assertSame([5 => ['id_item' => 0, 'modo' => 2, 'letra' => 'A', 'cantidad' => 10.0]], $r['a_tarifas_prev']);
        $this->assertSame(['id_ubi' => 77, 'id_items' => []], HashB::open($r['ctx_inc'], 'tarifa_ubi_update_inc'));
    }

    public function test_el_incremento_de_tarifas_se_aplica_sobre_la_actual_exista_o_no_la_prevista(): void
    {
        $this->tipoTarifa(5, 2, 'A', 10.0, 12.0);
        $this->tipoTarifa(6, 2, 'B', 20.0, null);

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv', 'inc_t' => 5]);

        $this->assertSame(10.5, $r['a_tarifas_prev'][5]['cantidad']);
        $this->assertSame(900, $r['a_tarifas_prev'][5]['id_item']);
        $this->assertSame(21.0, $r['a_tarifas_prev'][6]['cantidad']);
    }

    public function test_sin_tarifa_definida_en_la_casa_la_cantidad_actual_es_cero(): void
    {
        $this->tipoTarifa(5, 2, 'A', null, null);

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $this->assertSame(0.0, $r['a_tarifas_actual'][5]['cantidad']);
        $this->assertSame(0.0, $r['a_tarifas_prev'][5]['cantidad']);
    }

    public function test_sin_tipos_de_tarifa_las_actividades_salen_sin_ingresos_y_los_incrementos_no_estan_disponibles(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->ingresos->expects($this->never())->method('findById');

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv', 'G' => 10]);

        $fila = $r['a_actividades'][0];
        $this->assertSame(0, $fila['asistentes']);
        $this->assertSame('?', $fila['asistencias']);
        $this->assertSame('', $fila['id_tarifa']);
        $this->assertIsString($fila['ingresos']);
        $this->assertSame(0.0, $r['p_tia']);
        $this->assertSame(0.0, $r['p_tarifa']);
        $this->assertIsString($r['inc_p']);
        $this->assertSame(0, $r['inc_d']);
        $this->assertSame(0, $r['inc_pt']);
    }

    /**
     * Hallazgo (ya comentado en el código): todas las actividades se valoran con la última tarifa del listado de
     * tipos de tarifa de la sección, no con la tarifa de cada actividad.
     */
    public function test_todas_las_actividades_se_valoran_con_la_ultima_tarifa_del_listado(): void
    {
        $this->tipoTarifa(5, 2, 'A', 10.0, 12.0);
        $this->tipoTarifa(6, 2, 'B', 20.0, 25.0, 901);
        $a = $this->actividad(3001);
        $a->expects($this->never())->method('getTarifa');
        $this->actividadesDevueltas = [$a];
        $this->asistentes(30);

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $this->assertSame('B', $r['a_actividades'][0]['id_tarifa']);
        $this->assertSame(4125.0, $r['a_actividades'][0]['ingresos']);
        $this->assertSame(['id_ubi' => 77, 'id_items' => [900, 901]], HashB::open($r['ctx_inc'], 'tarifa_ubi_update_inc'));
    }

    public function test_varias_actividades_suman_dias_asistentes_e_ingresos(): void
    {
        $this->tipoTarifa(5, 2, 'A', 10.0, 12.0);
        $this->actividadesDevueltas = [$this->actividad(3001), $this->actividad(3002, 2.0)];
        $this->asistentes(30);

        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $this->assertSame(2, $r['p_tac']);
        // 5 días → 5,5; 2 días dentro del periodo con el mismo aumento (10 %) → 2,2
        $this->assertSame(7.7, $r['p_tda']);
        $this->assertSame(60, $r['p_tap']);
        $this->assertSame(231.0, $r['p_ta']);
        $this->assertSame(2772.0, $r['p_tia']);
    }

    public function test_sin_actividades_el_numero_de_actividades_es_1_para_no_dividir_por_cero(): void
    {
        $r = $this->uc()->execute(['id_ubi' => 77, 'seccion' => 'sv']);

        $this->assertSame(1, $r['p_tac']);
        $this->assertSame([], $r['a_actividades']);
    }
}
