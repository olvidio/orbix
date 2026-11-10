<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\XPermisos;
use src\shared\domain\criteria\Criteria;
use src\ubis\application\ListCtrData;
use src\ubis\application\services\UbiTelecoService;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;
use src\ubis\domain\value_objects\NumTelecoText;
use Tests\unit\ubis\support\UbiResolverFactory;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\Direccion;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Listado de centros y casas por localización y tipo de lista (home de ubis). */
final class ListCtrDataTest extends myTest
{
    private CentroRepositoryInterface&MockObject $centro;
    private CentroDlRepositoryInterface&MockObject $centroDl;
    private CentroExRepositoryInterface&MockObject $centroEx;
    private CasaRepositoryInterface&MockObject $casa;
    private CasaDlRepositoryInterface&MockObject $casaDl;
    private CasaExRepositoryInterface&MockObject $casaEx;
    private DescTelecoRepositoryInterface&MockObject $descTeleco;
    private ?TelecoCtrDlRepositoryInterface $telecoCtrDl = null;
    private mixed $sesionAnterior;
    private mixed $permAnterior;

    /** @var array<string, list<string>> repositorio => filtros de la última consulta */
    private array $filtros = [];
    /** @var array<string, list<string>> repositorio => orden de la última consulta */
    private array $ordenes = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        unset($_SESSION['oPerm']);

        $this->centro = $this->espia(CentroRepositoryInterface::class, 'Centro');
        $this->centroDl = $this->espia(CentroDlRepositoryInterface::class, 'CentroDl');
        $this->centroEx = $this->espia(CentroExRepositoryInterface::class, 'CentroEx');
        $this->casa = $this->espia(CasaRepositoryInterface::class, 'Casa');
        $this->casaDl = $this->espia(CasaDlRepositoryInterface::class, 'CasaDl');
        $this->casaEx = $this->espia(CasaExRepositoryInterface::class, 'CasaEx');
        $this->descTeleco = $this->createMock(DescTelecoRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        foreach (['session_auth' => $this->sesionAnterior, 'oPerm' => $this->permAnterior] as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    /**
     * @template T of object
     * @param class-string<T> $interfaz
     * @return T&MockObject
     */
    private function espia(string $interfaz, string $nombre): object
    {
        $repo = $this->createMock($interfaz);
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use ($nombre): array {
            $this->filtros[$nombre] = DescribeCriteria::filtros($c);
            $this->ordenes[$nombre] = DescribeCriteria::orden($c);

            return [];
        });

        return $repo;
    }

    private function uc(): ListCtrData
    {
        $repos = $this->telecoCtrDl === null ? [] : [TelecoCtrDlRepositoryInterface::class => $this->telecoCtrDl];
        $teleco = new UbiTelecoService(UbiResolverFactory::resolver($this, $repos), $this->descTeleco);

        return new ListCtrData($this->centro, $this->centroDl, $this->centroEx, $this->casa, $this->casaDl, $this->casaEx, $teleco);
    }

    // ---- elección de repositorio y criterios ----------------------------------------------------------------------

    public function test_la_propia_dl_usa_los_repositorios_dl_sin_filtrar_por_region(): void
    {
        $this->uc()->execute('H-dlbv', 'todos_ctr_dl');

        $this->assertSame(['active eq true'], $this->filtros['CentroDl']);
        $this->assertSame(['tipo_ctr asc', 'nombre_ubi asc'], $this->ordenes['CentroDl']);
        $this->assertArrayNotHasKey('Centro', $this->filtros);
    }

    public function test_otra_dl_usa_el_repositorio_general_y_filtra_por_region_y_dl(): void
    {
        $this->uc()->execute('H-dlpv', 'ctr_n');

        $this->assertSame(['active eq true', 'region eq H', 'dl eq dlp', 'tipo_ctr matches ^n'], $this->filtros['Centro']);
        $this->assertSame(['nombre_ubi asc'], $this->ordenes['Centro']);
    }

    public function test_una_comision_regional_no_filtra_por_dl(): void
    {
        $this->uc()->execute('H-crv', 'todos_ctr_dl');

        $this->assertSame(['active eq true', 'region eq H'], $this->filtros['Centro']);
    }

    public function test_en_sf_la_dl_no_pierde_su_ultima_letra(): void
    {
        $_SESSION['session_auth'] = ['sfsv' => 2, 'esquema' => 'H-dlbf'];

        $this->uc()->execute('H-dlp', 'todos_ctr_dl');

        $this->assertContains('dl eq dlp', $this->filtros['Centro']);
    }

    /**
     * @dataProvider tiposCentroProvider
     */
    public function test_cada_tipo_de_lista_filtra_por_su_expresion_de_tipo_ctr(string $lista, string $patron): void
    {
        $this->uc()->execute('H-dlbv', $lista);

        $this->assertContains("tipo_ctr matches $patron", $this->filtros['CentroDl']);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function tiposCentroProvider(): iterable
    {
        yield 'numerarios' => ['ctr_n', '^n'];
        yield 'nax' => ['ctr_nax', '^x'];
        yield 'agregados sin sp' => ['ctr_agd', '^a[^p]*'];
        yield 'supernumerarios' => ['ctr_s', '^(sj|sm|s)$'];
        yield 'sss+' => ['ctr_sssc', '^(ss|sss)$'];
        yield 'obras corporativas' => ['oc', '(oc)'];
        yield 'colegios' => ['cgi', '(cgi)'];
    }

    public function test_las_casas_de_retiros_filtran_por_tipo_y_en_sv_sin_permiso_solo_las_de_sv(): void
    {
        $this->uc()->execute('H-dlbv', 'cdr_cdc_dl');

        $this->assertSame(['active eq true', 'tipo_casa matches cdc|cdr', 'sv eq true'], $this->filtros['CasaDl']);
    }

    /**
     * @dataProvider permisosCasasProvider
     * @param list<string> $oficinas
     */
    public function test_con_permiso_sobre_sf_se_ven_las_casas_de_ambos(array $oficinas, bool $filtraSv): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $perm;

        $this->uc()->execute('H-dlbv', 'otros_cdc');

        $this->assertSame($filtraSv, in_array('sv eq true', $this->filtros['CasaDl'], true));
        $this->assertContains('tipo_casa not_matches cdc|cdr|cgi', $this->filtros['CasaDl']);
    }

    /**
     * @return iterable<string, array{list<string>, bool}>
     */
    public static function permisosCasasProvider(): iterable
    {
        yield 'vcsd' => [[MenuDlPermissionBits::VCSD], false];
        yield 'des' => [[MenuDlPermissionBits::DES], false];
        yield 'otro permiso' => [[MenuDlPermissionBits::SM], true];
    }

    public function test_en_sf_las_casas_se_filtran_por_sf(): void
    {
        $_SESSION['session_auth'] = ['sfsv' => 2, 'esquema' => 'H-dlbf'];

        $this->uc()->execute('H-dlbf', 'cdr_cdc_dl');

        $this->assertContains('sf eq true', $this->filtros['CasaDl']);
    }

    /**
     * @dataProvider listasExProvider
     */
    public function test_las_listas_de_fuera_usan_los_repositorios_ex(string $lista, string $repo, array $extra): void
    {
        $this->uc()->execute('ex', $lista);

        $this->assertSame(array_merge(['active eq true'], $extra), $this->filtros[$repo]);
    }

    /**
     * @return iterable<string, array{string, string, list<string>}>
     */
    public static function listasExProvider(): iterable
    {
        yield 'todos' => ['todos_ctr_ex', 'CentroEx', []];
        yield 'numerarios' => ['ctr_n', 'CentroEx', ['tipo_ctr matches ^n']];
        yield 'delegaciones' => ['dl', 'CentroEx', ['tipo_ctr eq dl']];
        yield 'comisiones' => ['cr', 'CentroEx', ['tipo_ctr eq cr']];
        yield 'casas' => ['cdc_ex', 'CasaEx', []];
    }

    public function test_una_lista_desconocida_no_consulta_ningun_repositorio(): void
    {
        $r = $this->uc()->execute('H-dlbv', 'inventada');

        $this->assertSame([], $this->filtros);
        $this->assertSame([], array_diff_key($r['a_valores'], ['select' => 1, 'scroll_id' => 1]));
    }

    /**
     * Hallazgo: las opciones de la lista de «otras» se llaman `ctr_n_ex`, `ctr_agd_ex` y `ctr_s_ex`, pero el
     * `switch` solo conoce `ctr_n`, `ctr_agd` y `ctr_s`: elegir una de ellas devuelve una lista vacía.
     *
     * @dataProvider opcionesExDesconocidasProvider
     */
    public function test_las_opciones_ex_con_sufijo_no_tienen_caso_y_dan_lista_vacia(string $lista): void
    {
        $r = $this->uc()->execute('ex', $lista);

        $this->assertArrayHasKey($lista, $r['opciones_que_lista'], 'la pantalla sí ofrece esta opción');
        $this->assertSame([], $this->filtros);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function opcionesExDesconocidasProvider(): iterable
    {
        yield 'n' => ['ctr_n_ex'];
        yield 'agd' => ['ctr_agd_ex'];
        yield 's' => ['ctr_s_ex'];
    }

    // ---- filas y opciones ----------------------------------------------------------------------

    private function centroDlConFila(): void
    {
        $dir1 = $this->createMock(Direccion::class);
        $dir1->method('getPoblacion')->willReturn('Madrid');
        $dir1->method('getPais')->willReturn('España');
        $dir1->method('getDireccionVo')->willReturn(null);
        $dir1->method('getC_p')->willReturn('28001');
        $dir2 = $this->createMock(Direccion::class);
        $dir2->method('getPoblacion')->willReturn('Roma');
        $dir2->method('getPais')->willReturn('Italia');
        $dir2->method('getDireccionVo')->willReturn(null);
        $dir2->method('getC_p')->willReturn('00100');
        $c = $this->createMock(CentroDl::class);
        $c->method('getRegion')->willReturn('H');
        $c->method('getId_ubi')->willReturn(1003);
        $c->method('getNombre_ubi')->willReturn('Centro A');
        $c->method('getTipo_ctr')->willReturn('ndl');
        $c->method('getDirecciones')->willReturn([$dir1, $dir2]);
        $this->centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centroDl->method('matching')->willReturn([$c]);
    }

    public function test_cada_ubi_es_una_fila_con_enlace_firmado_direcciones_juntas_y_telefonos(): void
    {
        $this->centroDlConFila();
        $t1 = $this->createMock(TelecoUbi::class);
        $t1->method('getNumTelecoVo')->willReturn(new NumTelecoText('91 000 00 00'));
        $t2 = $this->createMock(TelecoUbi::class);
        $t2->method('getNumTelecoVo')->willReturn(new NumTelecoText('91 111 11 11'));
        $this->telecoCtrDl = $this->createMock(TelecoCtrDlRepositoryInterface::class);
        $this->telecoCtrDl->method('matching')->willReturn([$t1, $t2]);

        $r = $this->uc()->execute('H-dlbv', 'todos_ctr_dl', '1003', '250');

        $this->assertSame(['select' => '1003', 'scroll_id' => '250'], array_intersect_key($r['a_valores'], ['select' => 1, 'scroll_id' => 1]));
        $fila = $r['a_valores'][1];
        $this->assertSame('1003', $fila['sel']);
        $this->assertNotSame('', $fila['ctx_trasladar']);
        $this->assertSame(['pau' => 'u', 'id_ubi' => 1003], $fila[1]['link_spec']['query']);
        $this->assertSame('Centro A', $fila[1]['valor']);
        $this->assertSame(['H', 'ndl', '<br>', '28001<br>00100', 'Madrid<br>Roma', '91 000 00 00 91 111 11 11'], [$fila[2], $fila[3], substr($fila[4], 0, 4), $fila[5], $fila[6], $fila[7]]);
    }

    public function test_sin_seleccion_ni_scroll_no_hay_meta(): void
    {
        $r = $this->uc()->execute('H-dlbv', 'todos_ctr_dl');

        $this->assertSame([], $r['a_valores']);
    }

    public function test_las_opciones_dependen_de_la_localizacion_y_de_sfsv(): void
    {
        $dl = $this->uc()->execute('H-dlbv', 'ctr_n');
        $ex = $this->uc()->execute('ex', 'ctr_n');
        $_SESSION['session_auth'] = ['sfsv' => 2, 'esquema' => 'H-dlbf'];
        $sf = $this->uc()->execute('H-dlb', 'ctr_n');

        $this->assertArrayHasKey('ctr_sssc', $dl['opciones_que_lista']);
        $this->assertArrayNotHasKey('ctr_nax', $dl['opciones_que_lista']);
        $this->assertArrayHasKey('ctr_nax', $sf['opciones_que_lista']);
        $this->assertArrayNotHasKey('ctr_sssc', $sf['opciones_que_lista']);
        $this->assertArrayHasKey('todos_ctr_ex', $ex['opciones_que_lista']);
        $this->assertArrayNotHasKey('todos_ctr_dl', $ex['opciones_que_lista']);
        $this->assertSame('otras', $ex['opciones_loc']['ex']);
        $this->assertNotSame('', $dl['ctx_resincronizar']);
    }

    public function test_el_boton_de_trasladar_solo_lo_ve_el_administrador_sv(): void
    {
        $this->assertCount(1, $this->uc()->execute('H-dlbv', 'ctr_n')['a_botones']);

        $perm = $this->createMock(XPermisos::class);
        $perm->method('only_perm')->willReturnCallback(fn (string $o): bool => $o === MenuDlPermissionBits::ADMIN_SV);
        $_SESSION['oPerm'] = $perm;

        $botones = $this->uc()->execute('H-dlbv', 'ctr_n')['a_botones'];

        $this->assertCount(2, $botones);
        $this->assertSame('fnjs_ver_dl()', $botones[1]['click']);
    }
}
