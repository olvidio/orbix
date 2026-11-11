<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\TiposActividades;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\casas\application\CasaIngresosListaData;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\entity\Ingreso;
use src\permisos\domain\PermisosActividades;
use src\procesos\domain\PermAccion;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\TarifaUbi;
use src\ubis\domain\value_objects\UbiNombreText;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Listado económico de actividades por casa: precio, ingresos previstos y reales, acumulados y totales sv/sf. */
final class CasaIngresosListaDataTest extends myTest
{
    private const TIPO_SV = 112025;
    private const TIPO_SF = 212025;
    private const PERIODO = ['periodo' => 'otro', 'empiezamin' => '1/1/2026', 'empiezamax' => '31/12/2026', 'year' => '2026'];

    private CasaDlRepositoryInterface&MockObject $casas;
    private TipoTarifaRepositoryInterface&MockObject $tiposTarifa;
    private ActividadRepositoryInterface&MockObject $actividades;
    private IngresoRepositoryInterface&MockObject $ingresos;
    private TarifaUbiRepositoryInterface&MockObject $tarifasUbi;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<string> */
    private array $filtros = [];
    /** @var list<string> */
    private array $filtrosTarifa = [];
    /** @var list<ActividadAll> */
    private array $actividadesDevueltas = [];
    /** @var list<TarifaUbi> */
    private array $tarifasDevueltas = [];
    /** @var array<int, ?Ingreso> */
    private array $ingresosPorActividad = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->anterior['oPermActividades'] = $_SESSION['oPermActividades'] ?? null;
        $this->permisoEconomico(3);

        $this->casas = $this->createMock(CasaDlRepositoryInterface::class);
        $this->casas->method('findById')->willReturnCallback(function (int $id): ?Casa {
            if ($id !== 77 && $id !== 78) {
                return null;
            }
            $c = $this->createMock(Casa::class);
            $c->method('getNombreUbiVo')->willReturn(new UbiNombreText($id === 77 ? 'Casa Alta' : 'Casa Baja'));

            return $c;
        });
        $this->tiposTarifa = $this->createMock(TipoTarifaRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return in_array('id_ubi eq 77', $this->filtros, true) ? $this->actividadesDevueltas : [];
        });
        $this->ingresos = $this->createMock(IngresoRepositoryInterface::class);
        $this->ingresos->method('findById')->willReturnCallback(fn (int $id): ?Ingreso => $this->ingresosPorActividad[$id] ?? null);
        $this->tarifasUbi = $this->createMock(TarifaUbiRepositoryInterface::class);
        $this->tarifasUbi->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosTarifa = DescribeCriteria::filtros($c);

            return $this->tarifasDevueltas;
        });
    }

    public function tearDown(): void
    {
        if ($this->anterior['oPermActividades'] === null) {
            unset($_SESSION['oPermActividades']);
        } else {
            $_SESSION['oPermActividades'] = $this->anterior['oPermActividades'];
        }
        parent::tearDown();
    }

    private function permisoEconomico(int $bits): void
    {
        $p = $this->createMock(PermisosActividades::class);
        $p->method('getPermisoActual')->willReturnCallback(
            static fn (string $afecta): PermAccion => new PermAccion($afecta === 'economic' ? $bits : 0)
        );
        $_SESSION['oPermActividades'] = $p;
    }

    private function uc(): CasaIngresosListaData
    {
        return new CasaIngresosListaData($this->casas, $this->tiposTarifa, $this->actividades, $this->ingresos, $this->tarifasUbi);
    }

    private function actividad(
        int $id,
        ?int $tarifa = 5,
        ?float $precio = null,
        float $enPeriodo = 5.0,
        int $tipo = self::TIPO_SV,
    ): ActividadAll&MockObject {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $a->method('getDl_org')->willReturn('dlb');
        $a->method('getTarifa')->willReturn($tarifa);
        $a->method('getPrecio')->willReturn($precio);
        $a->method('getF_ini')->willReturn(new DateTimeLocal('2026-02-10'));
        $a->method('getF_fin')->willReturn(new DateTimeLocal('2026-02-15'));
        $a->method('getDuracionAumentada')->willReturn(5.5);
        $a->method('getDuracionReal')->willReturn(5.0);
        $a->method('getDuracionEnPeriodo')->willReturn($enPeriodo);

        return $a;
    }

    private function tarifa(int $modo, float $cantidad): void
    {
        $t = $this->createMock(TipoTarifa::class);
        $t->method('getModo')->willReturn($modo);
        $this->tiposTarifa->method('findById')->willReturn($t);
        $tu = $this->createMock(TarifaUbi::class);
        $tu->method('getCantidad')->willReturn($cantidad);
        $this->tarifasDevueltas = [$tu];
    }

    private function ingreso(int $idActiv, ?int $previstos, ?int $asistentes, ?float $ingresos, ?string $observ = null): void
    {
        $i = new Ingreso();
        $i->setId_activ($idActiv);
        $i->setNumAsistentesPrevistosVo($previstos);
        $i->setNumAsistentesVo($asistentes);
        $i->setIngresosVo($ingresos);
        $i->setObservVo($observ);
        $this->ingresosPorActividad[$idActiv] = $i;
    }

    public function test_sin_ninguna_casa_valida_devuelve_error_y_no_consulta_actividades(): void
    {
        $this->actividades->expects($this->never())->method('matching');

        $r = $this->uc()->execute(['id_cdc' => ['0', '999']] + self::PERIODO);

        $this->assertFalse($r['ok']);
        $this->assertNotSame('', $r['error']);
        $this->assertCount(12, $r['a_cabeceras']);
        $this->assertSame([], $r['a_valores']);
        $this->assertSame([], $r['a_grupos']);
    }

    public function test_las_actividades_se_buscan_por_casa_y_periodo_sin_las_borrables(): void
    {
        $r = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO);

        $this->assertTrue($r['ok']);
        $this->assertSame([77 => 'Casa Alta'], $r['a_grupos']);
        $this->assertSame(
            ['f_ini between [2026-01-01,2026-12-31]', 'id_ubi eq 77', 'status lt 4', 'f_ini asc'],
            $this->filtros
        );
    }

    public function test_sin_actividades_cada_casa_lleva_solo_sus_tres_filas_de_totales(): void
    {
        $r = $this->uc()->execute(['id_cdc' => [77, 78]] + self::PERIODO);

        $this->assertSame([77, 78], array_keys($r['a_valores']));
        $filas = $r['a_valores'][78];
        $this->assertSame([0, 1, 2], array_keys($filas));
        $this->assertSame('1/1/2026', $filas[0][1]);
        $this->assertSame('31/12/2026', $filas[0][2]);
        $this->assertSame('-', $filas[0][7]);
        $this->assertSame('-', $filas[1][10]);
        $this->assertSame(0, $filas[2][5]);
        $this->assertSame(0.0, $filas[2][10]);
        $this->assertSame('', $r['errores']);
    }

    public function test_tarifa_por_actividad_modo_1_el_precio_es_la_cantidad_de_la_tarifa(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->tarifa(1, 100.0);
        $this->ingreso(3001, 30, 28, 2500.0, 'pagado en mano');

        $r = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO);

        $this->assertSame(['id_tarifa eq 5', 'id_ubi eq 77', 'year eq 2026'], $this->filtrosTarifa);
        $this->assertSame([
            1 => '10/2/2026',
            2 => '15/2/2026',
            3 => (new TiposActividades((string) self::TIPO_SV))->getNomGral(),
            4 => '100',
            5 => 30,
            6 => 28,
            7 => -2,
            8 => 3000.0,
            9 => 2500.0,
            10 => 3000.0,
            11 => 2500.0,
            12 => 'pagado en mano',
            'clase' => 'derecha',
        ], $r['a_valores'][77][0]);
        $this->assertSame('', $r['errores']);
    }

    public function test_tarifa_por_dia_el_precio_son_los_dias_ajustados_por_la_cantidad(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->tarifa(2, 100.0);
        $this->ingreso(3001, 30, 28, 2500.0);

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][0];

        // 5 días reales, 5,5 «aumentados» → 5,5 días × 100
        $this->assertStringContainsString('5.5', (string) $fila[4]);
        $this->assertStringContainsString('550', (string) $fila[4]);
        $this->assertSame(16500.0, $fila[8]);
    }

    public function test_un_precio_propio_de_la_actividad_prevalece_sobre_la_tarifa(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001, 5, 80.0)];
        $this->tarifa(1, 100.0);
        $this->ingreso(3001, 30, 28, 2500.0);

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][0];

        $this->assertSame(80.0, $fila[4]);
        $this->assertSame(2400.0, $fila[8]);
    }

    public function test_sin_tarifa_ni_precio_el_importe_previsto_es_cero(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001, null)];
        $this->tiposTarifa->expects($this->never())->method('findById');
        $this->ingreso(3001, 30, 28, 2500.0);

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][0];

        $this->assertSame(0.0, $fila[8]);
        $this->assertSame(2500.0, $fila[9]);
    }

    public function test_un_tipo_de_tarifa_que_ya_no_existe_se_trata_como_tarifa_por_dia(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->tiposTarifa->method('findById')->willReturn(null);
        $this->ingreso(3001, 30, 28, 2500.0);

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][0];

        $this->assertSame(0.0, $fila[8]);
    }

    public function test_una_actividad_a_caballo_del_periodo_cuenta_la_parte_proporcional_y_se_marca_con_asterisco(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001, 5, null, 2.5)];
        $this->tarifa(1, 100.0);
        $this->ingreso(3001, 30, 28, 2500.0);

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][0];

        $this->assertSame('50*', $fila[4]);
        $this->assertSame(1500.0, $fila[8]);
        $this->assertSame(1250.0, $fila[9]);
    }

    /**
     * Hallazgo: la «parte proporcional al periodo» se calcula con `getDuracionEnPeriodo($f_ini, $f_fin)` de la
     * propia actividad, no con el periodo consultado: la duración en periodo es siempre la duración real, el
     * factor es siempre 1 y el asterisco de la nota nunca aparece con datos reales.
     */
    public function test_la_duracion_en_periodo_se_pide_con_las_fechas_de_la_propia_actividad(): void
    {
        $a = $this->actividad(3001);
        $a->expects($this->once())->method('getDuracionEnPeriodo')->with(
            $this->callback(static fn (DateTimeLocal $d): bool => $d->getIso() === '2026-02-10'),
            $this->callback(static fn (DateTimeLocal $d): bool => $d->getIso() === '2026-02-15'),
        );
        $this->actividadesDevueltas = [$a];

        $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO);
    }

    public function test_los_datos_que_faltan_se_avisan_por_actividad_y_cuentan_como_cero(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001), $this->actividad(3002)];
        $this->tarifa(1, 100.0);
        $this->ingreso(3002, 0, null, null);

        $r = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO);

        // Tres avisos por actividad (previstos, asistentes, ingresos), unidos con <br>.
        $this->assertSame(5, substr_count($r['errores'], '<br>'));
        $fila = $r['a_valores'][77][0];
        $this->assertSame(0, $fila[5]);
        $this->assertSame(0, $fila[6]);
        $this->assertSame(0.0, $fila[9]);
        $this->assertSame('', $fila[12]);
    }

    public function test_los_acumulados_y_los_totales_separan_sv_y_sf(): void
    {
        $this->actividadesDevueltas = [
            $this->actividad(3001),
            $this->actividad(3002, 5, null, 5.0, self::TIPO_SF),
            $this->actividad(3003),
        ];
        $this->tarifa(1, 100.0);
        $this->ingreso(3001, 30, 28, 2500.0);
        $this->ingreso(3002, 10, 12, 1500.0);
        $this->ingreso(3003, 20, 20, 2000.0);

        $filas = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77];

        $this->assertSame([0, 1, 2, 3, 4, 5], array_keys($filas));
        $this->assertSame([3000.0, 4000.0, 6000.0], [$filas[0][10], $filas[1][10], $filas[2][10]]);
        $this->assertSame([2500.0, 4000.0, 6000.0], [$filas[0][11], $filas[1][11], $filas[2][11]]);

        $sv = $filas[3];
        $this->assertSame([50, 48, '80%', 5000.0, 4500.0, '75%', 4500.0], [$sv[5], $sv[6], $sv[7], $sv[8], $sv[9], $sv[10], $sv[11]]);
        $sf = $filas[4];
        $this->assertSame([10, 12, '20%', 1000.0, 1500.0, '25%', 1500.0], [$sf[5], $sf[6], $sf[7], $sf[8], $sf[9], $sf[10], $sf[11]]);
        $tot = $filas[5];
        $this->assertSame([60, 60, '', 6000.0, 6000.0, 6000.0, 6000.0], [$tot[5], $tot[6], $tot[7], $tot[8], $tot[9], $tot[10], $tot[11]]);
    }

    public function test_con_permiso_de_modificar_el_nombre_lleva_el_enlace_de_edicion(): void
    {
        $this->permisoEconomico(7);
        $this->actividadesDevueltas = [$this->actividad(3001)];

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][0];

        $this->assertSame(
            ['script' => 'fnjs_modificar(3001)', 'valor' => (new TiposActividades((string) self::TIPO_SV))->getNomGral()],
            $fila[3]
        );
    }

    public function test_sin_permiso_de_ver_lo_economico_la_actividad_no_sale_ni_suma(): void
    {
        $this->permisoEconomico(1);
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->ingreso(3001, 30, 28, 2500.0);

        $filas = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77];

        $this->assertCount(3, $filas);
        $this->assertSame(0, $filas[2][5]);
    }

    public function test_los_permisos_se_piden_para_cada_actividad(): void
    {
        $p = $this->createMock(PermisosActividades::class);
        $p->expects($this->once())->method('setActividad')->with(3001, (string) self::TIPO_SV, 'dlb');
        $p->method('getPermisoActual')->with('economic')->willReturn(new PermAccion(3));
        $_SESSION['oPermActividades'] = $p;
        $this->actividadesDevueltas = [$this->actividad(3001)];

        $this->assertCount(4, $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77]);
    }

    /**
     * Hallazgo: sin objeto de permisos en la sesión no se lista ninguna actividad (el listado de actividades por
     * casa, en cambio, lo trata como «se ve todo»).
     */
    public function test_sin_permisos_en_la_sesion_no_se_lista_ninguna_actividad(): void
    {
        unset($_SESSION['oPermActividades']);
        $this->actividadesDevueltas = [$this->actividad(3001)];

        $this->assertCount(3, $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77]);
    }

    public function test_una_actividad_sin_fechas_se_salta(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn(3001);
        $a->method('getId_tipo_activ')->willReturn(self::TIPO_SV);
        $a->method('getF_ini')->willReturn(null);
        $a->method('getF_fin')->willReturn(null);
        $this->actividadesDevueltas = [$a];

        $this->assertCount(3, $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77]);
    }

    public function test_desde_hoy_filtra_por_fecha_de_fin(): void
    {
        $this->uc()->execute(['id_cdc' => [77], 'periodo' => 'desdeHoy']);

        $this->assertStringStartsWith('f_fin between', $this->filtros[0]);
    }

    /**
     * Hallazgo: la tarifa de la casa se busca con el año del formulario tal cual; con un periodo por fechas (sin
     * año) se busca la tarifa del año «» y nunca se encuentra, aunque las fechas digan de qué año se trata.
     */
    public function test_sin_anyo_en_la_entrada_la_tarifa_se_busca_con_el_anyo_vacio(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001)];

        $this->uc()->execute(['id_cdc' => [77], 'year' => ''] + self::PERIODO);

        $this->assertSame(['id_tarifa eq 5', 'id_ubi eq 77', 'year eq '], $this->filtrosTarifa);
    }
}
