<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\TiposActividades;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\casas\application\CasaActividadesListaData;
use src\permisos\domain\PermisosActividades;
use src\personas\domain\entity\PersonaSacd;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\PermAccion;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\application\services\CentroNombreResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\value_objects\UbiNombreText;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Listado de actividades por casa y periodo: qué se enseña según los permisos de la sesión y las apps instaladas. */
final class CasaActividadesListaDataTest extends myTest
{
    private const TIPO = 112025;
    private const PERIODO = ['periodo' => 'otro', 'empiezamin' => '1/1/2026', 'empiezamax' => '31/12/2026'];

    private CasaDlRepositoryInterface&MockObject $casas;
    private TipoTarifaRepositoryInterface&MockObject $tarifas;
    private ActividadRepositoryInterface&MockObject $actividades;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private ActividadProcesoTareaRepositoryInterface&MockObject $procesos;
    private CentroDlRepositoryInterface&MockObject $centros;
    private ActividadCargoRepositoryInterface&MockObject $cargos;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var array<int, list<string>> filtros por casa */
    private array $filtros = [];
    /** @var array<int, list<ActividadAll>> */
    private array $actividadesPorCasa = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['session_auth', 'config', 'oPermActividades'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $_SESSION['session_auth']['sfsv'] = 1;
        $this->apps([]);

        $this->casas = $this->createMock(CasaDlRepositoryInterface::class);
        $this->casas->method('findById')->willReturnCallback(fn (int $id): ?Casa => match ($id) {
            77 => $this->casa('Casa Alta'),
            78 => $this->casa('Casa Baja'),
            default => null,
        });
        $this->tarifas = $this->createMock(TipoTarifaRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c): array {
            $filtros = DescribeCriteria::filtros($c);
            $idUbi = (int) substr($filtros[0], strlen('id_ubi eq '));
            $this->filtros[$idUbi] = array_merge($filtros, DescribeCriteria::orden($c));

            return $this->actividadesPorCasa[$idUbi] ?? [];
        });
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    /** @param list<string> $instaladas */
    private function apps(array $instaladas): void
    {
        $config = is_array($_SESSION['config'] ?? null) ? $_SESSION['config'] : [];
        $todas = ['procesos' => 911, 'actividadessacd' => 912];
        $config['a_apps'] = $todas;
        $config['app_installed'] = array_values(array_intersect_key($todas, array_flip($instaladas)));
        $_SESSION['config'] = $config;
    }

    /** @param array<string, int> $permisos bits de permiso por ámbito (datos, ctr, sacd) */
    private function permisosDeSesion(array $permisos): void
    {
        $p = $this->createMock(PermisosActividades::class);
        $p->method('getPermisoActual')->willReturnCallback(
            static fn (string $afecta): PermAccion => new PermAccion($permisos[$afecta] ?? 0)
        );
        $_SESSION['oPermActividades'] = $p;
    }

    private function casa(string $nombre): Casa&MockObject
    {
        $c = $this->createMock(Casa::class);
        $c->method('getNombreUbiVo')->willReturn(new UbiNombreText($nombre));

        return $c;
    }

    private function actividad(int $id, ?int $tarifa = null, ?string $hIni = '10:30:00', ?string $hFin = '17:00:00'): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getId_tipo_activ')->willReturn(self::TIPO);
        $a->method('getDl_org')->willReturn('dlb');
        $a->method('getF_ini')->willReturn(new DateTimeLocal('2026-02-10'));
        $a->method('getF_fin')->willReturn(new DateTimeLocal('2026-02-15'));
        $a->method('getH_ini')->willReturn($hIni === null ? null : TimeLocal::fromString($hIni));
        $a->method('getH_fin')->willReturn($hFin === null ? null : TimeLocal::fromString($hFin));
        $a->method('getTarifa')->willReturn($tarifa);
        $a->method('getObserv')->willReturn('traer sábanas');

        return $a;
    }

    private function uc(): CasaActividadesListaData
    {
        return new CasaActividadesListaData(
            $this->casas,
            $this->tarifas,
            $this->actividades,
            $this->encargados,
            $this->procesos,
            new CentroNombreResolver(
                $this->centros,
                $this->createMock(CentroEllosRepositoryInterface::class),
                $this->createMock(CentroEllasRepositoryInterface::class),
            ),
            $this->cargos,
        );
    }

    public function test_sin_casas_no_se_consulta_nada_pero_se_devuelven_las_cabeceras(): void
    {
        $this->actividades->expects($this->never())->method('matching');

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertTrue($r['ok']);
        $this->assertCount(13, $r['a_cabeceras']);
        $this->assertSame([], $r['a_valores']);
        $this->assertSame([], $r['a_grupos']);
    }

    public function test_los_grupos_son_las_casas_que_existen_y_cada_una_se_consulta_en_el_periodo(): void
    {
        $r = $this->uc()->execute(['id_cdc' => ['77', '0', 'x', '999', 78]] + self::PERIODO);

        $this->assertSame([77 => 'Casa Alta', 78 => 'Casa Baja'], $r['a_grupos']);
        $this->assertSame(
            ['id_ubi eq 77', 'f_ini between [2026-01-01,2026-12-31]', 'status lt 4', 'f_ini asc'],
            $this->filtros[77]
        );
        $this->assertArrayHasKey(78, $this->filtros);
        $this->assertSame([], $r['a_valores']);
    }

    public function test_fila_completa_sin_procesos_ni_sacd(): void
    {
        $this->actividadesPorCasa[77] = [$this->actividad(3001, 5)];
        $tarifa = $this->createMock(TipoTarifa::class);
        $tarifa->method('getLetra')->willReturn('B');
        $this->tarifas->expects($this->once())->method('findById')->with(5)->willReturn($tarifa);
        $ctr = $this->createMock(CentroDl::class);
        $ctr->method('getId_ubi')->willReturn(1003);
        $ctr2 = $this->createMock(CentroDl::class);
        $ctr2->method('getId_ubi')->willReturn(1004);
        $this->encargados->expects($this->once())->method('getCentrosEncargadosActividad')->with(3001)->willReturn([$ctr, $ctr2]);
        $centro = $this->createMock(CentroDl::class);
        $centro->method('getNombre_ubi')->willReturn('Brafa');
        $centro2 = $this->createMock(CentroDl::class);
        $centro2->method('getNombre_ubi')->willReturn('Dauradell');
        $this->centros->method('findById')->willReturnMap([[1003, $centro], [1004, $centro2]]);
        $this->cargos->expects($this->never())->method('getActividadSacds');
        $this->procesos->expects($this->never())->method('getSacdAprobado');

        $r = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO);

        $tipo = new TiposActividades((string) self::TIPO);
        $this->assertSame([
            1 => '10/2/2026',
            2 => '10:30',
            3 => '15/2/2026',
            4 => '17:00',
            5 => $tipo->getSfsvText(),
            6 => $tipo->getActividadText(),
            7 => $tipo->getAsistentesText(),
            8 => $tipo->getNom_tipoText(),
            9 => 'Casa Alta',
            10 => 'B',
            11 => 'Brafa; Dauradell',
            12 => '',
            13 => 'traer sábanas',
        ], $r['a_valores'][77][1]);
        $this->assertSame('sv', $tipo->getSfsvText());
    }

    public function test_sin_horas_ni_tarifa_las_celdas_quedan_vacias(): void
    {
        $this->actividadesPorCasa[77] = [$this->actividad(3001, null, null, null)];
        $this->tarifas->expects($this->never())->method('findById');

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1];

        $this->assertSame('', $fila[2]);
        $this->assertSame('', $fila[4]);
        $this->assertSame('', $fila[10]);
        $this->assertSame('', $fila[11]);
    }

    public function test_una_tarifa_que_ya_no_existe_deja_la_letra_vacia(): void
    {
        $this->actividadesPorCasa[77] = [$this->actividad(3001, 5)];
        $this->tarifas->method('findById')->willReturn(null);

        $this->assertSame('', $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1][10]);
    }

    public function test_con_procesos_sin_permiso_de_ocupado_la_actividad_no_sale(): void
    {
        $this->apps(['procesos']);
        $this->permisosDeSesion(['datos' => 0, 'ctr' => 31, 'sacd' => 31]);
        $this->actividadesPorCasa[77] = [$this->actividad(3001), $this->actividad(3002)];

        $r = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO);

        $this->assertSame([], $r['a_valores']);
        $this->assertSame([77 => 'Casa Alta'], $r['a_grupos']);
    }

    public function test_con_permiso_solo_de_ocupado_se_oculta_que_actividad_es(): void
    {
        $this->apps(['procesos']);
        $this->permisosDeSesion(['datos' => 1, 'ctr' => 0, 'sacd' => 0]);
        $this->actividadesPorCasa[77] = [$this->actividad(3001, 5)];
        $this->encargados->expects($this->never())->method('getCentrosEncargadosActividad');

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1];

        $this->assertSame('10/2/2026', $fila[1]);
        $this->assertSame('sv', $fila[5]);
        $this->assertSame('', $fila[6]);
        $this->assertSame('', $fila[7]);
        $this->assertSame('ocupado', $fila[8]);
        $this->assertSame('', $fila[11]);
        $this->assertSame('', $fila[13]);
    }

    public function test_con_procesos_y_permiso_de_ver_se_fija_la_actividad_en_los_permisos_de_la_sesion(): void
    {
        $this->apps(['procesos']);
        $p = $this->createMock(PermisosActividades::class);
        $p->expects($this->once())->method('setActividad')->with(3001, (string) self::TIPO, 'dlb');
        $p->method('getPermisoActual')->willReturn(new PermAccion(3));
        $_SESSION['oPermActividades'] = $p;
        $this->actividadesPorCasa[77] = [$this->actividad(3001)];

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1];

        $this->assertSame((new TiposActividades((string) self::TIPO))->getNom_tipoText(), $fila[8]);
        $this->assertSame('traer sábanas', $fila[13]);
    }

    public function test_con_procesos_pero_sin_permisos_en_la_sesion_se_ve_todo(): void
    {
        $this->apps(['procesos']);
        unset($_SESSION['oPermActividades']);
        $this->actividadesPorCasa[77] = [$this->actividad(3001)];

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1];

        $this->assertSame('traer sábanas', $fila[13]);
    }

    private function sacd(string $nombre): PersonaSacd&MockObject
    {
        $s = $this->createMock(PersonaSacd::class);
        $s->method('getPrefApellidosNombre')->willReturn($nombre);

        return $s;
    }

    public function test_con_actividadessacd_se_listan_los_sacd_separados_por_almohadilla(): void
    {
        $this->apps(['actividadessacd']);
        $this->actividadesPorCasa[77] = [$this->actividad(3001)];
        $this->cargos->expects($this->once())->method('getActividadSacds')->with(3001)
            ->willReturn([$this->sacd('Pla, Joan'), $this->sacd(''), new \stdClass(), $this->sacd('Roca, Pere')]);
        $this->procesos->expects($this->never())->method('getSacdAprobado');

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1];

        $this->assertSame('Pla, Joan# Roca, Pere', $fila[12]);
    }

    /**
     * @dataProvider aprobadoProvider
     */
    public function test_en_sf_con_procesos_los_sacd_solo_salen_si_estan_aprobados(?bool $aprobado, string $esperado): void
    {
        $this->apps(['procesos', 'actividadessacd']);
        $_SESSION['session_auth']['sfsv'] = 2;
        $this->permisosDeSesion(['datos' => 3, 'ctr' => 3, 'sacd' => 3]);
        $this->actividadesPorCasa[77] = [$this->actividad(3001)];
        $this->procesos->expects($this->once())->method('getSacdAprobado')->with(3001)->willReturn($aprobado);
        $this->cargos->method('getActividadSacds')->willReturn([$this->sacd('Pla, Joan')]);

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1];

        $this->assertSame($esperado, $fila[12]);
    }

    /**
     * @return iterable<string, array{?bool, string}>
     */
    public static function aprobadoProvider(): iterable
    {
        yield 'aprobado' => [true, 'Pla, Joan'];
        yield 'no aprobado' => [false, ''];
        yield 'sin fase de aprobación' => [null, ''];
    }

    public function test_en_sf_sin_procesos_los_sacd_salen_aunque_no_esten_aprobados(): void
    {
        $this->apps(['actividadessacd']);
        $_SESSION['session_auth']['sfsv'] = 2;
        $this->actividadesPorCasa[77] = [$this->actividad(3001)];
        $this->procesos->method('getSacdAprobado')->willReturn(false);
        $this->cargos->method('getActividadSacds')->willReturn([$this->sacd('Pla, Joan')]);

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1];

        $this->assertSame('Pla, Joan', $fila[12]);
    }

    /**
     * Hallazgo: el permiso «ver sacd» no oculta nada. La condición es «puede ver O hay nombres», así que con
     * sacd asignados salen siempre, tenga o no permiso quien consulta.
     */
    public function test_sin_permiso_de_ver_sacd_los_nombres_salen_igualmente(): void
    {
        $this->apps(['procesos', 'actividadessacd']);
        $this->permisosDeSesion(['datos' => 3, 'ctr' => 3, 'sacd' => 0]);
        $this->actividadesPorCasa[77] = [$this->actividad(3001)];
        $this->cargos->method('getActividadSacds')->willReturn([$this->sacd('Pla, Joan')]);

        $fila = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO)['a_valores'][77][1];

        $this->assertSame('Pla, Joan', $fila[12]);
    }

    /**
     * Hallazgo: el índice de fila se incrementa antes del filtro de permisos, así que las filas ocultas dejan huecos
     * en la numeración (aquí la única visible es la 2).
     */
    public function test_las_filas_ocultas_dejan_huecos_en_la_numeracion(): void
    {
        $this->apps(['procesos']);
        $p = $this->createMock(PermisosActividades::class);
        $actual = 0;
        $p->method('setActividad')->willReturnCallback(function (int $id) use (&$actual): void {
            $actual = $id;
        });
        $p->method('getPermisoActual')->willReturnCallback(function () use (&$actual): PermAccion {
            return new PermAccion($actual === 3001 ? 0 : 3);
        });
        $_SESSION['oPermActividades'] = $p;
        $this->actividadesPorCasa[77] = [$this->actividad(3001), $this->actividad(3002)];

        $r = $this->uc()->execute(['id_cdc' => [77]] + self::PERIODO);

        $this->assertSame([2], array_keys($r['a_valores'][77]));
    }
}
