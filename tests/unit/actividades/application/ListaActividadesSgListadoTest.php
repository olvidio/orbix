<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\application\ListaActividadesSgListado;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;
use src\personas\domain\entity\PersonaSacd;
use src\procesos\domain\PermAccion;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\entity\CentroDl;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Listado de actividades de sg (crt/cv de s y sg): filtros, filas con lugar,
 * centros, sacd y coincidencia, nombre oculto de la otra sección, permisos
 * (ocupado / sin permiso) y demasiados resultados.
 *
 * Extiende myTest porque el lugar se busca con Ubi::newUbi (localizador estático).
 */
final class ListaActividadesSgListadoTest extends myTest
{
    private ActividadRepositoryInterface&MockObject $actividades;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private ActividadCargoRepositoryInterface&MockObject $cargos;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<list<string>> */
    private array $consultas = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oPerm', 'oPermActividades'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $this->oficinas([]);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
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

    /** @param list<string> $nombres */
    private function apps(array $nombres): void
    {
        $ids = ['procesos' => 3, 'actividadessacd' => 7, 'actividadescentro' => 8];
        $a = array_intersect_key($ids, array_flip($nombres));
        $_SESSION['config'] = ['a_apps' => $a, 'app_installed' => array_values($a)];
    }

    /** @param list<string> $oficinas */
    private function oficinas(array $oficinas): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $p): bool => in_array($p, $oficinas, true));
        $_SESSION['oPerm'] = $perm;
    }

    private function uc(): ListaActividadesSgListado
    {
        return new ListaActividadesSgListado($this->actividades, $this->createMock(PreferenciaRepositoryInterface::class), $this->encargados, $this->cargos);
    }

    /** @param list<ActividadAll> $lista */
    private function devolver(array $lista): void
    {
        $this->actividades->method('matching')->willReturnCallback(function ($c) use ($lista): array {
            $this->consultas[] = DescribeCriteria::filtros($c);
            return $lista;
        });
    }

    private function actividad(int $id, int $tipo = 141000, ?int $ubi = null, string $lugar = '', ?float $precio = null): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setId_tipo_activ($tipo);
        $a->setNom_activ("crt $id");
        $a->setDl_org('dlb');
        $a->setF_ini(new DateTimeLocal('2027-03-05'));
        $a->setF_fin(new DateTimeLocal('2027-03-08'));
        $a->setId_ubi($ubi);
        $a->setLugar_esp($lugar);
        $a->setPrecio($precio);
        return $a;
    }

    /** @return list<array<int|string, mixed>> */
    private static function filas(array $out): array
    {
        return array_values(array_filter($out['a_valores'], 'is_int', ARRAY_FILTER_USE_KEY));
    }

    public function test_filtros_por_defecto_crt_y_con_campos(): void
    {
        $this->devolver([]);
        $out = $this->uc()->ejecutar([], 0);
        $this->assertSame('1[45]1', $out['id_tipo_activ']);
        $this->assertSame(['status eq 2', 'id_tipo_activ matches ^1[45]1'], array_slice($this->consultas[0], 0, 2));
        $this->assertStringStartsWith('f_ini between [', $this->consultas[0][2]);
        $this->assertSame(['cargos', 'asistentes', 'lista', 'ctrs org'], array_column($out['a_botones'], 'txt'));
        $this->assertSame('0 actividades encontradas (0 sin permiso)', $out['result_busqueda']);

        $this->uc()->ejecutar(['tipo_activ_sg' => 'cv', 'status' => '5', 'id_ubi' => '200', 'dl_org' => 'dlp', 'periodo' => 'otro', 'empiezamin' => '1/3/2027', 'empiezamax' => '31/3/2027'], 0);
        $this->assertSame(['id_tipo_activ matches ^1[45]3', 'id_ubi eq 200', 'f_ini between [2027-03-01,2027-03-31]', 'dl_org eq dlp'], $this->consultas[1]);
    }

    public function test_tipo_desconocido_usa_crt_y_desde_hoy_filtra_por_fin(): void
    {
        $this->devolver([]);
        $this->uc()->ejecutar(['tipo_activ_sg' => 'otro', 'periodo' => 'desdeHoy'], 0);
        $this->assertSame('id_tipo_activ matches ^1[45]1', $this->consultas[0][1]);
        $this->assertStringStartsWith('f_fin between [', $this->consultas[0][2]);
    }

    public function test_fila_con_lugar_centros_sacd_y_coincidencia(): void
    {
        $this->apps(['actividadessacd', 'actividadescentro']);
        $a = $this->actividad(10, ubi: 1, lugar: 'Montaña', precio: 120.0);
        $this->devolver([$a]);
        $this->actividades->method('getCoincidencia')->with($a)->willReturn(true);
        $c1 = new CentroDl();
        $c1->setNombre_ubi('Ctr A');
        $c2 = new CentroDl();
        $c2->setNombre_ubi('Ctr B');
        $this->encargados->method('getCentrosEncargadosActividad')->with(10)->willReturn([$c1, $c2]);
        $s1 = new PersonaSacd();
        $s1->setNom('Juan');
        $s1->setApellido1('García');
        $s2 = new PersonaSacd();
        $s2->setNom('Pedro');
        $s2->setApellido1('López');
        $this->cargos->method('getActividadSacds')->with(10)->willReturn([$s1, $s2]);

        $out = $this->uc()->ejecutar(['sel' => ['10#crt 10'], 'scroll_id' => '3'], 0);

        $fila = self::filas($out)[0];
        $this->assertSame('10#crt 10', $fila['sel']);
        $this->assertSame(['id_activ' => 10], HashB::open($fila['ctx_duplicar'], 'actividad_duplicar'));
        $this->assertSame(['*', 'crt', 's', 'Montaña', 'Ctr A, Ctr B', 120.0], [$fila[3], $fila[4], $fila[5], $fila[6], $fila[7], $fila[9]]);
        $this->assertSame((new DateTimeLocal('2027-03-05'))->getFromLocal(), $fila[1]);
        $this->assertStringContainsString('García', $fila[8]);
        $this->assertStringContainsString('# ', $fila[8]);
        $this->assertStringEndsWith('Pedro', rtrim($fila[8]));
        $this->assertSame(['10#crt 10'], $out['a_valores']['select']);
        $this->assertSame('3', $out['a_valores']['scroll_id']);
        $this->assertSame('1 actividades encontradas (0 sin permiso)', $out['result_busqueda']);
    }

    public function test_lugar_sin_determinar_y_casa_inexistente(): void
    {
        $this->devolver([$this->actividad(10), $this->actividad(11, ubi: 999999991)]);
        $filas = self::filas($this->uc()->ejecutar([], 0));
        $this->assertSame('sin determinar', $filas[0][6]);
        $this->assertSame('', $filas[1][6]);
        $this->assertSame('', $filas[0][7], 'sin la app no hay centros');
        $this->assertSame('', $filas[0][8], 'sin la app no hay sacd');
    }

    public function test_actividad_de_sf_muestra_solo_el_tipo_salvo_a_des(): void
    {
        $this->devolver([$this->actividad(10, 241000)]);
        $this->assertSame('10#sf crt', self::filas($this->uc()->ejecutar([], 0))[0]['sel']);
        $this->oficinas(['des']);
        $this->assertSame('10#crt 10', self::filas($this->uc()->ejecutar([], 0))[0]['sel']);
    }

    private function permisos(array $porActividad, array $sacd = []): void
    {
        $actual = 0;
        $perm = $this->createMock(PermisosActividades::class);
        $perm->method('setActividad')->willReturnCallback(function (int $id) use (&$actual): void {
            $actual = $id;
        });
        $perm->method('getPermisoActual')->willReturnCallback(function (string $que) use (&$actual, $porActividad, $sacd): PermAccion {
            $lista = $que === 'sacd' ? ($sacd[$actual] ?? []) : ($porActividad[$actual] ?? []);
            $a = $this->createMock(PermAccion::class);
            $a->method('have_perm_activ')->willReturnCallback(fn (string $p): bool => in_array($p, $lista, true));
            $a->method('have_perm_action')->willReturnCallback(fn (string $p): bool => in_array($p, $lista, true));
            return $a;
        });
        $_SESSION['oPermActividades'] = $perm;
    }

    public function test_con_procesos_ocupado_sin_permiso_y_sacd_ocultos(): void
    {
        $this->apps(['procesos', 'actividadessacd']);
        $this->permisos([10 => ['ocupado', 'ver'], 11 => ['ocupado'], 12 => []], [10 => []]);
        $this->devolver([$this->actividad(10), $this->actividad(11), $this->actividad(12)]);
        $this->cargos->expects($this->never())->method('getActividadSacds');
        $out = $this->uc()->ejecutar([], 0);
        $filas = self::filas($out);
        $this->assertCount(2, $filas);
        $this->assertSame('10#crt 10', $filas[0]['sel']);
        $this->assertSame('', $filas[0][8], 'sin permiso de ver sacd');
        $this->assertSame('', $filas[1]['sel']);
        // Hallazgo menor: el texto «ocupado» va en la columna de inicio.
        $this->assertStringStartsWith('ocupado sv (', $filas[1][1]);
        $this->assertSame('3 actividades encontradas (1 sin permiso)', $out['result_busqueda']);
    }

    /**
     * Hallazgo: con procesos instalado y sin objeto de permisos en la sesión, las
     * actividades se saltan sin contarlas ni como «sin permiso» (la búsqueda
     * general, ActividadSelectListado, las muestra todas en ese caso).
     */
    public function test_con_procesos_sin_permisos_en_sesion_no_sale_nada_ni_se_cuenta(): void
    {
        $this->apps(['procesos']);
        unset($_SESSION['oPermActividades']);
        $this->devolver([$this->actividad(10)]);
        $out = $this->uc()->ejecutar([], 0);
        $this->assertSame([], self::filas($out));
        $this->assertSame('0 actividades encontradas (0 sin permiso)', $out['result_busqueda']);
    }

    public function test_demasiadas_actividades_pide_confirmar(): void
    {
        $this->devolver(array_map(fn (int $i) => $this->actividad($i), range(1, 201)));
        $out = $this->uc()->ejecutar([], 7);
        $this->assertSame([], $out['a_valores']);
        $this->assertSame(201, $out['advertencia_demasiadas']['num_actividades']);
        $this->assertSame(['continuar' => 'si', 'stack' => 7], $out['advertencia_demasiadas']['continuar_link_spec']['query']);
        $this->assertCount(201, self::filas($this->uc()->ejecutar(['continuar' => 'si'], 7)));
    }
}
