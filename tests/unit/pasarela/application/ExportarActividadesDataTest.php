<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\pasarela\application\Conversiones;
use src\pasarela\application\ExportarActividadesData;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Listado de exportación de actividades: filtro, fila por actividad, activación, tarifas y contribuciones. */
final class ExportarActividadesDataTest extends myTest
{
    private CasaDlRepositoryInterface&MockObject $casas;
    private CentroDlRepositoryInterface&MockObject $centros;
    private ActividadDlRepositoryInterface&MockObject $actividades;
    private TipoTarifaRepositoryInterface&MockObject $tiposTarifa;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private TarifaUbiRepositoryInterface&MockObject $tarifas;
    private RelacionTarifaTipoActividadRepositoryInterface&MockObject $relaciones;
    private Conversiones&MockObject $conv;
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1];
        $this->casas = $this->createMock(CasaDlRepositoryInterface::class);
        $this->casas->method('getArrayCasas')->willReturn([1003 => 'Casa A']);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centros->method('getArrayCentros')->willReturn([501 => 'agdCentro X', 502 => 'sgCentro Y']);
        $this->actividades = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->tiposTarifa = $this->createMock(TipoTarifaRepositoryInterface::class);
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->encargados->method('matching')->willReturn([]);
        $this->tarifas = $this->createMock(TarifaUbiRepositoryInterface::class);
        $this->relaciones = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $this->conv = $this->createMock(Conversiones::class);
        $this->conv->method('getArrayNombre')->willReturn([131001 => 'crt de n']);
        $this->conv->method('getArrayTipo')->willReturn([131001 => 'curso retiro']);
        $this->conv->method('getArrayPerfil')->willReturn([131001 => 'N']);
        $this->conv->method('getArrayActivacion')->willReturn([131001 => 10]);
        $this->conv->method('getArrayContribucionReserva')->willReturn([131001 => 20]);
        $this->conv->method('getArrayContribucionNoDuerme')->willReturn([131001 => 50]);
    }

    public function tearDown(): void
    {
        if ($this->sesionAnterior === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesionAnterior;
        }
        parent::tearDown();
    }

    private function uc(): ExportarActividadesData
    {
        return new ExportarActividadesData($this->casas, $this->centros, $this->actividades, $this->tiposTarifa, $this->encargados, $this->tarifas, $this->relaciones, $this->conv);
    }

    private function actividad(int $tipo = 131001, ?int $tarifa = 3, ?int $plazas = 40, float $dias = 7.0): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $a->method('getId_activ')->willReturn(3001);
        $a->method('getId_ubi')->willReturn(1003);
        $a->method('getF_ini')->willReturn(new DateTimeLocal('2026-08-10'));
        $a->method('getF_fin')->willReturn(new DateTimeLocal('2026-08-17'));
        $a->method('getH_ini')->willReturn(TimeLocal::fromString('10:00'));
        $a->method('getH_fin')->willReturn(null);
        $a->method('getTarifa')->willReturn($tarifa);
        $a->method('getPlazas')->willReturn($plazas);
        $a->method('getDuracion')->willReturn($dias);

        return $a;
    }

    private function tarifa(int $serie, float $cantidad): TarifaUbi&MockObject
    {
        $t = $this->createMock(TarifaUbi::class);
        $t->method('getId_serie')->willReturn($serie);
        $t->method('getCantidad')->willReturn($cantidad);

        return $t;
    }

    private const FILTRO = ['id_tipo_activ' => '131001', 'inicio_iso' => '2026-08-01', 'fin_iso' => '2026-08-31'];

    public function test_sin_periodo_valido_no_se_consulta_nada(): void
    {
        $this->actividades->expects($this->never())->method('matching');

        foreach ([['id_tipo_activ' => '131001'], ['id_tipo_activ' => '131001', 'inicio_iso' => '2026-08-01'], ['inicio_iso' => '', 'fin_iso' => '2026-08-31']] as $in) {
            $r = $this->uc()->execute($in);

            $this->assertSame([[], [], []], [$r['a_cabeceras'], $r['a_botones'], $r['a_valores']]);
            $this->assertNotSame('', $r['errores']);
        }
    }

    public function test_el_filtro_usa_prefijo_de_tipo_periodo_y_casas(): void
    {
        $filtros = null;
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros): array {
            $filtros = DescribeCriteria::filtros($c);

            return [];
        });

        $r = $this->uc()->execute(self::FILTRO + ['id_cdc' => ['1003', '1004']]);

        $this->assertSame(20, count($r['a_cabeceras']));
        $this->assertSame(['id_tipo_activ matches ^131001', 'f_ini between [2026-08-01,2026-08-31]', 'id_ubi in [1003,1004]'], $filtros);
    }

    public function test_el_tipo_todas_no_filtra_y_sin_tipo_se_compone_desde_sfsv_perfil_y_actividad(): void
    {
        $filtros = [];
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros): array {
            $filtros[] = DescribeCriteria::filtros($c);

            return [];
        });

        $this->uc()->execute(['id_tipo_activ' => '......', 'inicio_iso' => '2026-08-01', 'fin_iso' => '2026-08-31', 'id_cdc' => 'x']);
        $this->uc()->execute(['inicio_iso' => '2026-08-01', 'fin_iso' => '2026-08-31']);

        $this->assertSame(['f_ini between [2026-08-01,2026-08-31]'], $filtros[0]);
        $this->assertStringStartsWith('id_tipo_activ matches ^', $filtros[1][0]);
    }

    public function test_una_fila_completa_con_activacion_numerica_y_tarifa_de_la_casa(): void
    {
        $this->actividades->method('matching')->willReturn([$this->actividad()]);
        $this->tarifas->method('matching')->willReturn([$this->tarifa(1, 20.0), $this->tarifa(2, 10.0)]);

        $r = $this->uc()->execute(self::FILTRO);

        $this->assertSame('', $r['errores']);
        $f = $r['a_valores'][1];
        $this->assertSame(['Casa A', 'N', 'Crt de n', 'curso retiro'], [$f[2], $f[3], $f[4], $f[5]]);
        $this->assertSame(['10/8/2026', '10:00', '17/8/2026', null], [$f[6], $f[7], $f[8], $f[9]]);
        $this->assertSame('31/7/2026', $f[10], 'activación = inicio − 10 días');
        $this->assertSame(40, $f[11]);
        $this->assertSame([140.0, 70.0, 70.0], [$f[18], $f[19], $f[20]], 'general = días × tarifa; no duerme = 50 %');
        $this->assertSame(20, $f[17]);
        $this->assertSame('', $f[15]);
    }

    public function test_activacion_upload_usa_la_fecha_de_hoy(): void
    {
        $this->conv = $this->createMock(Conversiones::class);
        foreach (['getArrayNombre', 'getArrayTipo', 'getArrayPerfil', 'getArrayContribucionReserva', 'getArrayContribucionNoDuerme'] as $m) {
            $this->conv->method($m)->willReturn([]);
        }
        $this->conv->method('getArrayActivacion')->willReturn([131001 => 'upload']);
        $this->actividades->method('matching')->willReturn([$this->actividad()]);
        $this->tarifas->method('matching')->willReturn([$this->tarifa(1, 1.0)]);

        $r = $this->uc()->execute(self::FILTRO);

        $this->assertSame((new DateTimeLocal())->getFromLocal(), $r['a_valores'][1][10]);
        $this->assertSame('?', $r['a_valores'][1][20], 'sin porcentaje de no duerme no se calcula');
    }

    public function test_una_activacion_no_valida_marca_la_fecha_con_interrogante_y_avisa(): void
    {
        $this->conv = $this->createMock(Conversiones::class);
        foreach (['getArrayNombre', 'getArrayTipo', 'getArrayPerfil', 'getArrayContribucionReserva', 'getArrayContribucionNoDuerme'] as $m) {
            $this->conv->method($m)->willReturn([]);
        }
        $this->conv->method('getArrayActivacion')->willReturn([131001 => 'raro']);
        $this->actividades->method('matching')->willReturn([$this->actividad()]);
        $this->tarifas->method('matching')->willReturn([$this->tarifa(1, 1.0)]);

        $r = $this->uc()->execute(self::FILTRO);

        $this->assertSame('?', $r['a_valores'][1][10]);
        $this->assertStringContainsString('<br>', $r['errores']);
    }

    public function test_sin_tarifa_en_la_actividad_se_busca_la_del_tipo_y_si_no_hay_se_avisa(): void
    {
        $this->actividades->method('matching')->willReturn([$this->actividad(131001, null)]);
        $rel = $this->createMock(RelacionTarifaTipoActividad::class);
        $rel->method('getId_tarifa')->willReturn(9);
        $this->relaciones->method('matching')->willReturnOnConsecutiveCalls([$rel], []);
        $consultadas = [];
        $this->tarifas->method('matching')->willReturnCallback(function (Criteria $c) use (&$consultadas): array {
            $consultadas[] = DescribeCriteria::filtros($c);

            return [$this->tarifa(1, 5.0)];
        });

        $con = $this->uc()->execute(self::FILTRO);
        $sin = $this->uc()->execute(self::FILTRO);

        $this->assertContains('id_tarifa eq 9', $consultadas[0]);
        $this->assertSame('', $con['errores']);
        $this->assertContains('id_tarifa eq 0', $consultadas[1]);
        $this->assertStringContainsString('<br>', $sin['errores']);
    }

    public function test_si_la_casa_no_tiene_esa_tarifa_se_avisa_y_las_contribuciones_son_cero(): void
    {
        $this->actividades->method('matching')->willReturn([$this->actividad()]);
        $this->tarifas->method('matching')->willReturn([]);
        $t = $this->createMock(TipoTarifa::class);
        $t->method('getLetra')->willReturn('B');
        $this->tiposTarifa->method('findById')->with(3)->willReturn($t);

        $r = $this->uc()->execute(self::FILTRO);

        $this->assertStringContainsString('B', $r['errores']);
        $this->assertStringContainsString('Casa A', $r['errores']);
        $this->assertSame([0.0, 0.0], [$r['a_valores'][1][18], $r['a_valores'][1][19]]);
    }

    public function test_la_contribucion_obligatoria_depende_del_tipo_y_las_casas_sin_nombre_salen_con_interrogantes(): void
    {
        $this->casas = $this->createMock(CasaDlRepositoryInterface::class);
        $this->casas->method('getArrayCasas')->willReturn([]);
        $this->actividades->method('matching')->willReturn([$this->actividad(131001), $this->actividad(231001)]);
        $this->tarifas->method('matching')->willReturn([$this->tarifa(1, 1.0)]);

        $r = $this->uc()->execute(self::FILTRO);

        $this->assertNotSame($r['a_valores'][1][16], $r['a_valores'][2][16]);
        $this->assertSame('??', $r['a_valores'][1][2]);
    }

    public function test_los_organizadores_salen_sin_sg_ni_agd_y_con_interrogante_si_no_se_conocen(): void
    {
        $this->actividades->method('matching')->willReturn([$this->actividad()]);
        $this->tarifas->method('matching')->willReturn([$this->tarifa(1, 1.0)]);
        $mk = function (int $ubi): CentroEncargado {
            $e = $this->createMock(CentroEncargado::class);
            $e->method('getId_ubi')->willReturn($ubi);

            return $e;
        };
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->encargados->method('matching')->willReturn([$mk(501), $mk(502), $mk(999)]);

        $f = $this->uc()->execute(self::FILTRO)['a_valores'][1];

        $this->assertSame(['Centro X', 'Centro Y', '?'], [$f[12], $f[13], $f[14]]);
    }
}
