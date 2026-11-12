<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\application\CalendarioListasDatos;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\asistentes\application\services\AsistenteActividadService;
use src\permisos\domain\PermisosActividades;
use src\procesos\domain\PermAccion;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\services\CentroNombreResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Calendario de listas: por casas (grupos = casas, filas con tarifa, asistentes,
 * centros encargados, permisos) y por oficina (grupos = tipos de asistentes según
 * la oficina y sus permisos).
 */
final class CalendarioListasDatosModosTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private CasaDlRepositoryInterface&MockObject $casasDl;
    private TipoTarifaRepositoryInterface&MockObject $tarifas;
    private ActividadRepositoryInterface&MockObject $actividades;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private CasaRepositoryInterface&MockObject $casas;
    private CentroRepositoryInterface&MockObject $centros;
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private AsistenteActividadService&MockObject $asistentes;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<list<string>> */
    private array $consultas = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oPermActividades', 'iPermMenus', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $this->casasDl = $this->createMock(CasaDlRepositoryInterface::class);
        $this->tarifas = $this->createMock(TipoTarifaRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->casas = $this->createMock(CasaRepositoryInterface::class);
        $this->centros = $this->createMock(CentroRepositoryInterface::class);
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->asistentes = $this->createMock(AsistenteActividadService::class);
        $tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipos->method('getAsistentesPosibles')->willReturn([1 => 'n', 2 => 'nax', 3 => 'agd', 6 => 'sssc']);
        $tipos->method('getNom_tipoPosibles')->willReturn(['tipo_nom' => [], 'nom_tipo' => []]);
        $this->sustituirServicio(TipoDeActividadRepositoryInterface::class, $tipos);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function uc(): CalendarioListasDatos
    {
        return new CalendarioListasDatos(
            $this->casasDl, $this->tarifas, $this->actividades, $this->encargados, $this->casas, $this->centros,
            new CentroNombreResolver($this->centrosDl, $this->createMock(CentroEllosRepositoryInterface::class), $this->createMock(CentroEllasRepositoryInterface::class)),
            $this->asistentes,
        );
    }

    private function actividad(int $id, int $tipo = 112021, ?int $ubi = 10200, string $lugar = '', ?int $tarifa = null): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setId_tipo_activ($tipo);
        $a->setNom_activ("act $id");
        $a->setDl_org('dlb');
        $a->setF_ini(new DateTimeLocal('2027-03-05'));
        $a->setF_fin(new DateTimeLocal('2027-03-08'));
        $a->setId_ubi($ubi);
        $a->setLugar_esp($lugar);
        $a->setTarifa($tarifa);
        return $a;
    }

    /** @param callable(list<string>): list<ActividadAll> $fn */
    private function actividadesPor(callable $fn): void
    {
        $this->actividades->method('matching')->willReturnCallback(function ($c) use ($fn): array {
            $f = DescribeCriteria::filtros($c);
            $this->consultas[] = $f;
            return $fn($f);
        });
    }

    private function casa(int $id, string $nombre): Casa
    {
        $c = new Casa();
        $c->setId_ubi($id);
        $c->setNombre_ubi($nombre);
        return $c;
    }

    // --- Por casas ---

    public function test_lista_de_casas_elegidas_con_sus_actividades(): void
    {
        $this->casasDl->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['id_ubi in [10200,10201]'], DescribeCriteria::filtros($c));
            return [$this->casa(10200, 'Casa Uno'), $this->casa(10201, 'Casa Dos')];
        });
        $this->actividadesPor(fn (array $f): array => in_array('id_ubi eq 10200', $f, true) ? [$this->actividad(10, tarifa: 4)] : []);
        $t = new TipoTarifa();
        $t->setLetraVo('B');
        $this->tarifas->method('findById')->with(4)->willReturn($t);
        $this->asistentes->method('getAsistentesDeActividad')->willReturn([1, 2, 3]);

        $html = $this->uc()->ejecutar(['que' => 'lista_cdc', 'id_cdc' => ['10200', '10201'], 'periodo' => 'otro', 'empiezamin' => '1/3/2027', 'empiezamax' => '31/3/2027'])['html'];

        $this->assertSame(['f_ini between [2027-03-01,2027-03-31]', 'status lt 4', 'id_ubi eq 10200'], $this->consultas[0]);
        $this->assertStringContainsString('Casa Uno', $html);
        $this->assertStringContainsString('Casa Dos', $html);
        $this->assertStringContainsString('n ca', $html);
        $this->assertStringContainsString('>3<', $html);
        $this->assertStringContainsString('>B<', $html);
    }

    public static function providerCasas(): \Generator
    {
        yield 'comunes' => ['c_comunes', ['tipo_ubi eq cdcdl', 'sv eq true', 'sf eq true']];
        yield 'comunes sf' => ['c_comunes_sf', ['tipo_ubi eq cdcdl', 'sv eq true', 'sf eq true']];
        yield 'todas' => ['c_todas', ['tipo_ubi eq cdcdl']];
        yield 'todas sf' => ['c_todas_sf', ['tipo_ubi eq cdcdl', 'sf eq true']];
        yield 'todas sv' => ['c_todas_sv', ['tipo_ubi eq cdcdl', 'sv eq true']];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerCasas')]
    public function test_seleccion_de_casas(string $que, array $filtros): void
    {
        $this->casasDl->expects($this->once())->method('matching')->willReturnCallback(function ($c) use ($filtros): array {
            $this->assertSame($filtros, DescribeCriteria::filtros($c));
            return [];
        });
        $this->uc()->ejecutar(['que' => $que]);
    }

    public function test_lugar_especial_sin_lugar_y_centros_encargados(): void
    {
        $this->casasDl->method('matching')->willReturn([$this->casa(10200, 'Casa Uno')]);
        $this->actividadesPor(fn (): array => [$this->actividad(10, ubi: null, lugar: 'Montaña'), $this->actividad(11, ubi: null)]);
        $enc = new CentroEncargado();
        $enc->setId_activ(10);
        $enc->setId_ubi(300);
        $this->encargados->method('matching')->willReturn([$enc]);
        $ctr = new CentroDl();
        $ctr->setNombre_ubi('Ctr Encargado');
        $this->centrosDl->method('findById')->with(300)->willReturn($ctr);

        $html = $this->uc()->ejecutar(['que' => 'c_todas', 'ver_ctr' => 'si'])['html'];

        $this->assertStringContainsString('La actividad: act 11  No tiene lugar asignado', $html);
        $this->assertStringContainsString('Ctr Encargado', $html);
    }

    public function test_con_procesos_ocupado_y_sin_permiso(): void
    {
        $_SESSION['config'] = ['a_apps' => ['procesos' => 3], 'app_installed' => [3]];
        $actual = 0;
        $perm = $this->createMock(PermisosActividades::class);
        $perm->method('setActividad')->willReturnCallback(function (int $id) use (&$actual): void {
            $actual = $id;
        });
        $perm->method('getPermisoActual')->willReturnCallback(function () use (&$actual): PermAccion {
            $a = $this->createMock(PermAccion::class);
            $a->method('have_perm_action')->willReturnCallback(fn (string $p): bool => match ($actual) {
                10 => $p === 'ocupado',
                default => false,
            });
            return $a;
        });
        $_SESSION['oPermActividades'] = $perm;
        $this->casasDl->method('matching')->willReturn([$this->casa(10200, 'Casa Uno')]);
        $this->actividadesPor(fn (): array => [$this->actividad(10), $this->actividad(11, 133000)]);

        $html = $this->uc()->ejecutar(['que' => 'c_todas'])['html'];

        $this->assertStringContainsString('ocupado', $html);
        $this->assertStringNotContainsString('n ca', $html);
        $this->assertStringNotContainsString('agd cv', $html, 'sin permiso ni de ocupado no sale');
    }

    // --- Por oficina ---

    public function test_todas_las_oficinas_un_grupo_por_tipo_de_asistentes(): void
    {
        $this->actividadesPor(fn (array $f): array => in_array('id_tipo_activ matches ^13', $f, true) ? [$this->actividad(10, 133000)] : []);
        $html = $this->uc()->ejecutar(['que' => 'o_todas'])['html'];
        $this->assertCount(4, $this->consultas);
        $this->assertSame(['id_tipo_activ matches ^11', 'id_tipo_activ matches ^12', 'id_tipo_activ matches ^13', 'id_tipo_activ matches ^16'], array_map(fn ($f) => end($f), $this->consultas));
        $this->assertStringContainsString('agd cv', $html);
    }

    private function oficina(string $oficina, int $bits): void
    {
        $_SESSION['session_auth']['mi_oficina'] = $oficina;
        $_SESSION['iPermMenus'] = $bits;
    }

    public function test_oficina_sm_solo_ve_los_grupos_de_sus_permisos(): void
    {
        // Permiso de sm (n) y no de des: el grupo sssc se quita (antes se buscaba «sss+» y se quedaba).
        // nax no tiene oficina asociada: se queda siempre.
        $this->oficina('sm', 1 << 7);
        $this->actividadesPor(fn (): array => []);
        $this->uc()->ejecutar(['que' => 'o_actual']);
        $this->assertSame(['id_tipo_activ matches ^11', 'id_tipo_activ matches ^12'], array_map(fn ($f) => end($f), $this->consultas));
    }

    public function test_oficina_sg_un_solo_grupo_de_s_y_sg(): void
    {
        $this->oficina('sg', 1 << 6);
        $this->actividadesPor(fn (): array => [$this->actividad(10, 141000)]);
        $html = $this->uc()->ejecutar(['que' => 'o_actual'])['html'];
        $this->assertSame(['id_tipo_activ matches ^1[45]'], array_map(fn ($f) => end($f), $this->consultas));
        $this->assertStringContainsString('s y sg', $html);
    }

    public function test_oficina_des_un_solo_grupo_con_sssc_y_crt_de_s_y_sg(): void
    {
        $this->oficina('des', 1 << 3);
        $this->actividadesPor(fn (array $f): array => end($f) === 'id_tipo_activ matches ^16' ? [$this->actividad(10, 161000)] : [$this->actividad(11, 141000)]);
        $html = $this->uc()->ejecutar(['que' => 'o_actual'])['html'];
        $this->assertSame(['id_tipo_activ matches ^16', 'id_tipo_activ matches ^1(124)|^1(.41)'], array_map(fn ($f) => end($f), $this->consultas));
        $this->assertSame(1, substr_count($html, '<h2>'), 'un solo grupo');
        $this->assertSame(1, substr_count($html, '<td>sssc crt ?</td>'), 'cada actividad sale una sola vez');
        $this->assertSame(1, substr_count($html, '<td>s crt ?</td>'), 'cada actividad sale una sola vez');
    }
}
