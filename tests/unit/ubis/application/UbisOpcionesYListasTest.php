<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\XPermisos;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\CalendarioPeriodosGet2Data;
use src\ubis\application\CasasOpcionesData;
use src\ubis\application\CentrosOpcionesData;
use src\ubis\application\services\CasasDropdown;
use src\ubis\application\services\CentrosDropdown;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\application\services\DelegacionQuery;
use src\ubis\application\services\RegionDropdown;
use src\ubis\application\services\TipoCasaDropdown;
use src\ubis\application\services\TipoCentroDropdown;
use src\ubis\application\UbisEditarOpcionesData;
use src\ubis\application\UbisListaData;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CasaPeriodo;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\entity\Direccion;
use src\ubis\domain\value_objects\DelegacionId;
use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Opciones de desplegables, listado de ubis y calendario de periodos de una casa. */
final class UbisOpcionesYListasTest extends myTest
{
    private mixed $configAnterior;
    private mixed $permAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
    }

    public function tearDown(): void
    {
        foreach (['oConfig' => $this->configAnterior, 'oPerm' => $this->permAnterior] as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    // ---- calendario ----------------------------------------------------------------------

    private function periodo(int $id, ?string $ini, ?string $fin, ?int $sfsv = 1): CasaPeriodo
    {
        $p = $this->createMock(CasaPeriodo::class);
        $p->method('getId_item')->willReturn($id);
        $p->method('getF_ini')->willReturn($ini === null ? null : new DateTimeLocal($ini));
        $p->method('getF_fin')->willReturn($fin === null ? null : new DateTimeLocal($fin));
        $p->method('getSfsv')->willReturn($sfsv);

        return $p;
    }

    public function test_sin_casa_o_anyo_no_se_consulta_y_se_ofrece_crear_uno_nuevo(): void
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->expects($this->never())->method('matching');

        foreach ([[0, 2026], [1003, 0]] as [$ubi, $anyo]) {
            $r = (new CalendarioPeriodosGet2Data($repo))->execute($ubi, $anyo);

            $this->assertSame([], $r['a_valores']);
            $this->assertCount(3, $r['a_cabeceras']);
            $this->assertTrue($r['show_nuevo']);
            $this->assertSame('', $r['overlap_error']);
        }
    }

    public function test_los_periodos_del_anyo_salen_con_su_script_de_modificar_y_el_texto_sfsv(): void
    {
        $criterio = null;
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterio): array {
            $criterio = DescribeCriteria::filtros($c);

            return [$this->periodo(7, '2026-02-01', '2026-02-10'), $this->periodo(8, null, '2026-03-01'), $this->periodo(9, '2026-06-01', '2026-06-05', 2)];
        });

        $r = (new CalendarioPeriodosGet2Data($repo))->execute(1003, 2026);

        $this->assertSame(['id_ubi eq 1003', 'f_ini gte 2026-1-1', 'f_fin lte 2026-12-31'], $criterio);
        $this->assertSame([1, 3], array_keys($r['a_valores']), 'el periodo sin fecha se omite pero conserva su número');
        $this->assertSame(['1/2/2026', '10/2/2026'], [$r['a_valores'][1][1], $r['a_valores'][1][2]]);
        $this->assertSame('fnjs_modificar(7)', $r['a_valores'][1][3]['script']);
        $this->assertNotSame($r['a_valores'][1][3]['valor'], $r['a_valores'][3][3]['valor'], 'sv y sf se distinguen');
        // Entre febrero y junio quedan días libres: `comprobarSolapes` lo avisa además de los solapes.
        $this->assertStringContainsString('10/2/2026', $r['overlap_error']);
    }

    public function test_periodos_consecutivos_sin_huecos_ni_solapes_no_dan_aviso(): void
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->method('matching')->willReturn([$this->periodo(7, '2026-01-01', '2026-06-30'), $this->periodo(8, '2026-07-01', '2026-12-31')]);

        $this->assertSame('', (new CalendarioPeriodosGet2Data($repo))->execute(1003, 2026)['overlap_error']);
    }

    public function test_dos_periodos_solapados_se_senalan(): void
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->method('matching')->willReturn([$this->periodo(7, '2026-02-01', '2026-02-10'), $this->periodo(8, '2026-02-05', '2026-02-20')]);

        $this->assertNotSame('', (new CalendarioPeriodosGet2Data($repo))->execute(1003, 2026)['overlap_error']);
    }

    // ---- casas y centros ----------------------------------------------------------------------

    /**
     * @dataProvider filtrosProvider
     * @param array<string, mixed> $filtro
     */
    public function test_el_filtro_de_casas_y_centros_se_traduce_a_la_misma_condicion(array $filtro, string $condicion): void
    {
        $casas = $this->createMock(CasaDlRepositoryInterface::class);
        $casas->expects($this->once())->method('getArrayCasas')->with($condicion)->willReturn([1 => 'Casa A']);
        $centros = $this->createMock(CentroDlRepositoryInterface::class);
        $centros->expects($this->once())->method('getArrayCentros')->with($condicion)->willReturn([2 => 'Ctr B']);

        $this->assertSame(['opciones' => [1 => 'Casa A']], (new CasasOpcionesData(new CasasDropdown($casas)))->execute($filtro));
        $this->assertSame(['opciones' => [2 => 'Ctr B']], (new CentrosOpcionesData(new CentrosDropdown($centros)))->execute($filtro));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string}>
     */
    public static function filtrosProvider(): iterable
    {
        yield 'por defecto solo activas' => [[], "WHERE active = 't'"];
        yield 'incluir inactivas' => [['active' => false], ''];
        yield 'sv y sf' => [['sv' => 1, 'sf' => 1], "WHERE active = 't' AND sv = 't' AND sf = 't'"];
        yield 'ids válidos' => [['id_ubi_in' => ['5', 7, 'x', 0, -3, [1]]], "WHERE active = 't' AND id_ubi IN (5,7)"];
        yield 'ids pedidos pero ninguno válido: no mostrar nada' => [['id_ubi_in' => ['x', 0]], "WHERE active = 't' AND FALSE"];
        yield 'lista de ids vacía' => [['active' => false, 'id_ubi_in' => []], 'WHERE FALSE'];
    }

    // ---- delegaciones ----------------------------------------------------------------------

    private function delegacion(int $id, ?string $dl): Delegacion&MockObject
    {
        $d = $this->createMock(Delegacion::class);
        $d->method('getIdDlVo')->willReturn(new DelegacionId($id));
        $d->method('getDlVo')->willReturn(new DelegacionCode($dl));

        return $d;
    }

    public function test_dl_por_region_stgr_filtra_activas_y_omite_las_sin_codigo(): void
    {
        $criterios = [];
        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterios): array {
            $criterios[] = DescribeCriteria::filtros($c);

            return [$this->delegacion(3, 'dlb'), $this->delegacion(4, null), $this->delegacion(5, 'dlp')];
        });
        $q = new DelegacionQuery($repo);

        $this->assertSame([3 => 'dlb', 5 => 'dlp'], $q->arrayDlByRegionStgr());
        $q->arrayDlByRegionStgr(['H', 'M']);

        $this->assertSame(['active eq true'], $criterios[0]);
        $this->assertSame(['active eq true', 'region_stgr in [H,M]'], $criterios[1]);
    }

    // ---- UbisEditarOpcionesData ----------------------------------------------------------------------

    private function editarOpciones(?CentroDlRepositoryInterface $dl = null, ?CentroExRepositoryInterface $ex = null, array $delegaciones = []): UbisEditarOpcionesData
    {
        $delRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delRepo->method('matching')->willReturn($delegaciones);
        $tipoCentro = $this->createMock(TipoCentroRepositoryInterface::class);
        $tipoCentro->method('getArrayTiposCentro')->willReturn(['ctrdl' => 'dl']);
        $tipoCasa = $this->createMock(TipoCasaRepositoryInterface::class);
        $tipoCasa->method('getArrayTiposCasa')->willReturn(['cdcdl' => 'casa dl']);

        return new UbisEditarOpcionesData(
            $dl ?? $this->createMock(CentroDlRepositoryInterface::class),
            $ex ?? $this->createMock(CentroExRepositoryInterface::class),
            new DelegacionDropdown($delRepo),
            new RegionDropdown($this->createMock(RegionRepositoryInterface::class)),
            new TipoCentroDropdown($tipoCentro),
            new TipoCasaDropdown($tipoCasa)
        );
    }

    /**
     * @dataProvider padresProvider
     */
    public function test_el_centro_padre_se_busca_por_dl_o_region_con_codigos_validos(string $tipo, string $dlIn, string $regionIn, ?string $where): void
    {
        $dl = $this->createMock(CentroDlRepositoryInterface::class);
        $ex = $this->createMock(CentroExRepositoryInterface::class);
        $consultas = [];
        $dl->method('getArrayCentros')->willReturnCallback(function (string $w) use (&$consultas): array {
            $consultas[] = ['dl', $w];

            return [1 => 'A'];
        });
        $ex->method('getArrayCentros')->willReturnCallback(function (string $w) use (&$consultas): array {
            $consultas[] = ['ex', $w];

            return [2 => 'B'];
        });

        $r = $this->editarOpciones($dl, $ex)->execute('CentroDl', $tipo, $dlIn, $regionIn);

        if ($where === null) {
            $this->assertSame([], $consultas);
            $this->assertSame([], $r['opciones_id_ctr_padre']);

            return;
        }
        $this->assertSame([[str_starts_with($tipo, 'ctrex') ? 'ex' : 'dl', $where]], $consultas);
    }

    /**
     * @return iterable<string, array{string, string, string, ?string}>
     */
    public static function padresProvider(): iterable
    {
        yield 'ctrdl con dl' => ['ctrdl', 'dlb', 'H', "WHERE dl = 'dlb'"];
        yield 'ctrsf con región' => ['ctrsf', '', 'H', "WHERE region = 'H'"];
        yield 'ctrex' => ['ctrex', 'dlb', '', "WHERE dl = 'dlb'"];
        yield 'sin dl ni región' => ['ctrdl', '', '', ''];
        yield 'dl con inyección se ignora' => ['ctrdl', "x' OR '1'='1", 'H', "WHERE region = 'H'"];
        yield 'todo inválido' => ['ctrdl', 'a b', 'H;DROP', ''];
        yield 'tipo sin padre' => ['cdcdl', 'dlb', 'H', null];
    }

    public function test_las_opciones_incluyen_tipos_y_regiones(): void
    {
        $r = $this->editarOpciones()->execute('CentroDl', 'cdcdl', '', '');

        $this->assertSame(['ctrdl' => 'dl'], $r['opciones_tipo_ctr']);
        $this->assertSame(['cdcdl' => 'casa dl'], $r['opciones_tipo_casa']);
        $this->assertSame([], $r['opciones_region']);
    }

    // ---- UbisListaData ----------------------------------------------------------------------

    private function conSfsv(int $sfsv): void
    {
        // El 7.º argumento del snapshot es el ámbito; sfsv sale de la sesión de autenticación.
        $_SESSION['session_auth'] = ['sfsv' => $sfsv];
    }

    /**
     * @return array{0: CasaRepositoryInterface&MockObject, 1: CentroRepositoryInterface&MockObject}
     */
    private function repos(array $casas = [], array $centros = []): array
    {
        $c = $this->createMock(CasaRepositoryInterface::class);
        $c->method('matching')->willReturn($casas);
        $x = $this->createMock(CentroRepositoryInterface::class);
        $x->method('matching')->willReturn($centros);

        return [$c, $x];
    }

    private function ubi(string $clase, int $id, string $nombre, string $tipo, ?Direccion $direccion = null): MockObject
    {
        $u = $this->createMock($clase);
        $u->method('getId_ubi')->willReturn($id);
        $u->method('getNombre_ubi')->willReturn($nombre);
        $u->method('getTipo_ubi')->willReturn($tipo);
        $u->method('getDl')->willReturn('dlb');
        $u->method('getRegion')->willReturn('H');
        $u->method('getDirecciones')->willReturn($direccion === null ? [] : [$direccion]);

        return $u;
    }

    public function test_un_sfsv_desconocido_devuelve_error(): void
    {
        $this->conSfsv(0);
        [$c, $x] = $this->repos();

        $r = (new UbisListaData($c, $x))->execute('');

        $this->assertNotSame('', $r['error']);
        $this->assertSame([], $r['a_valores']);
    }

    public function test_el_listado_mezcla_casas_y_centros_ordenados_con_orden_natural(): void
    {
        $this->conSfsv(1);
        $dir = $this->createMock(Direccion::class);
        $dir->method('getDireccionVo')->willReturn(null);
        $dir->method('getC_p')->willReturn('28001');
        $dir->method('getPoblacion')->willReturn('Madrid');
        [$c, $x] = $this->repos(
            [$this->ubi(Casa::class, 12, 'casa 10', 'cdcdl'), $this->ubi(Casa::class, 11, 'casa 9', 'cdcdl', $dir)],
            [$this->ubi(Centro::class, 20, 'Centro B', 'ctrdl')]
        );

        $r = (new UbisListaData($c, $x))->execute('');

        $this->assertSame(['casa 9', 'casa 10', 'Centro B'], array_column($r['a_valores'], 1), 'casa 9 antes que casa 10; sin distinguir mayúsculas');
        $this->assertSame([11, 'cdc', 'dlb', 'H', '', '28001', 'Madrid'], [
            $r['a_valores'][0]['sel'], $r['a_valores'][0][2], $r['a_valores'][0][3], $r['a_valores'][0][4],
            $r['a_valores'][0][5], $r['a_valores'][0][6], $r['a_valores'][0][7],
        ]);
        $this->assertSame(['', '', ''], [$r['a_valores'][1][5], $r['a_valores'][1][6], $r['a_valores'][1][7]]);
        $this->assertCount(7, $r['a_cabeceras']);
    }

    /**
     * @dataProvider filtrosListaProvider
     */
    public function test_los_filtros_de_casas_dependen_de_sfsv_y_permisos(int $sfsv, array $oficinas, string $nombre, array $esperados, array $noEsperados): void
    {
        $this->conSfsv($sfsv);
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $perm;
        $casas = $this->createMock(CasaRepositoryInterface::class);
        $filtrosCasa = [];
        $casas->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtrosCasa): array {
            $filtrosCasa = DescribeCriteria::filtros($c);

            return [];
        });
        $centros = $this->createMock(CentroRepositoryInterface::class);
        $filtrosCtr = [];
        $centros->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtrosCtr): array {
            $filtrosCtr = DescribeCriteria::filtros($c);

            return [];
        });

        (new UbisListaData($casas, $centros))->execute($nombre);

        foreach ($esperados as $e) {
            $this->assertNotSame([], preg_grep('/^' . preg_quote($e, '/') . '/', $filtrosCasa), $e);
        }
        foreach ($noEsperados as $n) {
            $this->assertSame([], preg_grep('/^' . preg_quote($n, '/') . '/', $filtrosCasa), $n);
        }
        $this->assertContains('active eq true', $filtrosCtr);
        $this->assertContains('cdc eq true', $filtrosCtr);
    }

    /**
     * @return iterable<string, array{int, list<string>, string, list<string>, list<string>}>
     */
    public static function filtrosListaProvider(): iterable
    {
        yield 'sv sin permiso solo ve casas sv' => [1, [], '', ['sv eq true', 'active eq true'], ['sf eq true']];
        yield 'sv con vcsd ve también sf' => [1, [MenuDlPermissionBits::VCSD], '', ['active eq true'], ['sv eq true', 'sf eq true']];
        yield 'sv con des ve también sf' => [1, [MenuDlPermissionBits::DES], '', ['active eq true'], ['sv eq true']];
        yield 'sf solo casas sf' => [2, [], '', ['sf eq true', 'active eq true'], ['sv eq true']];
        yield 'con nombre se añade la búsqueda sin acentos' => [1, [], 'ari+', ['nombre_ubi'], []];
    }
}
