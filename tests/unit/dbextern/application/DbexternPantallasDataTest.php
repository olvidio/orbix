<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\dbextern\application\SincroIndexData;
use src\dbextern\application\support\SincroDBFactory;
use src\dbextern\application\VerDesaparecidosDeListasData;
use src\dbextern\application\VerDesaparecidosDeOrbixData;
use src\dbextern\application\VerListasData;
use src\dbextern\application\VerOrbixData;
use src\dbextern\application\VerOrbixOtraDlData;
use src\dbextern\application\VerTrasladosData;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\CopiarBDU;
use src\dbextern\domain\entity\IdMatchPersona;
use src\dbextern\domain\entity\PersonaBDU;
use src\dbextern\domain\SincroDB;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\XPermisos;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Pantallas de la sincronización con la BDU: índice con contadores y listados de cada grupo. */
final class DbexternPantallasDataTest extends myTest
{
    private IdMatchPersonaRepositoryInterface&MockObject $idMatch;
    private PersonaBDURepositoryInterface&MockObject $bduRepo;
    private SincroDB&MockObject $sincro;
    private SincroDBFactory&MockObject $factory;
    private PersonaNRepositoryInterface&MockObject $repoN;
    private PersonaRepositoryResolver $resolver;
    private mixed $permAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
        $this->idMatch = $this->createMock(IdMatchPersonaRepositoryInterface::class);
        $this->bduRepo = $this->createMock(PersonaBDURepositoryInterface::class);
        $this->sincro = $this->createMock(SincroDB::class);
        $this->factory = $this->createMock(SincroDBFactory::class);
        $this->factory->method('create')->willReturn($this->sincro);
        $this->repoN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->resolver = new PersonaRepositoryResolver(
            $this->repoN,
            $this->createMock(PersonaAgdRepositoryInterface::class),
            $this->createMock(PersonaNaxRepositoryInterface::class),
            $this->createMock(PersonaSRepositoryInterface::class),
            $this->createMock(PersonaSSSCRepositoryInterface::class),
            $this->createMock(PersonaExRepositoryInterface::class),
            $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
        );
    }

    public function tearDown(): void
    {
        if ($this->permAnterior === null) {
            unset($_SESSION['oPerm']);
        } else {
            $_SESSION['oPerm'] = $this->permAnterior;
        }
        parent::tearDown();
    }

    private function bdu(int $id, string $apenom = 'Gil, Ana', string $dl = 'dlb'): PersonaBDU&MockObject
    {
        $p = $this->createMock(PersonaBDU::class);
        $p->method('getIdentif')->willReturn($id);
        $p->method('getApenom')->willReturn($apenom);
        $p->method('getNombre')->willReturn('Ana');
        $p->method('getApellido1')->willReturn('de Gil');
        $p->method('getApellido1_sinprep')->willReturn('Gil');
        $p->method('getApellido2')->willReturn('Paz');
        $p->method('getApellido2_sinprep')->willReturn('Paz');
        $p->method('getFecha_Naci')->willReturn('15/3/1980');
        $p->method('getDl')->willReturn($dl);

        return $p;
    }

    private function match(int $listas, ?int $orbix): IdMatchPersona&MockObject
    {
        $m = $this->createMock(IdMatchPersona::class);
        $m->method('getId_listas')->willReturn($listas);
        $m->method('getId_orbix')->willReturn($orbix);

        return $m;
    }

    private function persona(int $id, string $situacion = 'A'): PersonaN&MockObject
    {
        $p = $this->createMock(PersonaN::class);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getSituacion')->willReturn($situacion);
        $p->method('getPrefApellidosNombre')->willReturn("Apellidos $id");
        $p->method('getNom')->willReturn('Eva');
        $p->method('getApellido1')->willReturn('Gil');
        $p->method('getNx1')->willReturn('');
        $p->method('getApellido2')->willReturn('Paz');
        $p->method('getNx2')->willReturn('');
        $p->method('getF_nacimiento')->willReturn(null);

        return $p;
    }

    // ---- VerListasData ----------------------------------------------------------------------

    private function verListas(): VerListasData
    {
        return new VerListasData($this->idMatch, $this->factory);
    }

    public function test_el_listado_de_listas_omite_las_ya_unidas(): void
    {
        $this->sincro->method('getPersonasBDU')->willReturn([$this->bdu(100001), $this->bdu(100002)]);
        $this->idMatch->method('matching')->willReturnCallback(
            fn (Criteria $c): array => in_array('id_listas eq 100001', DescribeCriteria::filtros($c), true) ? [$this->match(100001, 1)] : []
        );

        $r = ($this->verListas())('H', 'dlb', 'n', false);

        $this->assertSame([1], array_keys($r['lista']));
        $this->assertSame(100002, $r['lista'][1]['id_nom_listas']);
        $this->assertSame(['Gil, Ana', 'Ana', 'de Gil', 'Gil', 'Paz', 'Paz', '15/3/1980'], [
            $r['lista'][1]['ape_nom'], $r['lista'][1]['nombre'], $r['lista'][1]['apellido1'], $r['lista'][1]['apellido1_sinprep'],
            $r['lista'][1]['apellido2'], $r['lista'][1]['apellido2_sinprep'], $r['lista'][1]['f_nacimiento'],
        ]);
        $this->assertSame(0, $r['cont_sync']);
        $this->assertNotSame('', $r['ctx_crear_todos']);
    }

    public function test_en_la_primera_carga_la_union_automatica_cuenta_y_oculta_las_unidas(): void
    {
        $this->sincro->method('getPersonasBDU')->willReturn([$this->bdu(100001), $this->bdu(100002), $this->bdu(100003)]);
        $this->idMatch->method('matching')->willReturn([]);
        $this->sincro->method('union_automatico')->willReturnCallback(function (PersonaBDU $p): bool {
            if ($p->getIdentif() === 100003) {
                throw new \RuntimeException('falla una');
            }

            return $p->getIdentif() === 100001;
        });

        $r = ($this->verListas())('H', 'dlb', 'n', true);

        $this->assertSame(1, $r['cont_sync']);
        $this->assertSame([100002, 100003], array_column($r['lista'], 'id_nom_listas'), 'una unión que falla no aborta el listado');
    }

    public function test_los_posibles_destinos_de_otra_dl_solo_se_buscan_si_no_hay_de_la_misma(): void
    {
        $this->sincro->method('posiblesOrbix')->willReturnOnConsecutiveCalls([['id' => 1]], []);
        $this->sincro->expects($this->once())->method('posiblesOrbixOtrasDl')->with(100001)->willReturn([['id' => 2]]);

        $con = $this->verListas()->getPosiblesMatches('n', 'H', 'dlb', 100001);
        $sin = $this->verListas()->getPosiblesMatches('n', 'H', 'dlb', 100001);

        $this->assertSame([[['id' => 1]], []], [$con['posibles_misma_dl'], $con['posibles_otra_dl']]);
        $this->assertSame([[], [['id' => 2]]], [$sin['posibles_misma_dl'], $sin['posibles_otra_dl']]);
        foreach (['ctx_crear', 'ctx_unir', 'ctx_crear_todos'] as $k) {
            $this->assertNotSame('', $con[$k]);
        }
    }

    // ---- VerOrbixData ----------------------------------------------------------------------

    public function test_orbix_con_un_tipo_desconocido_devuelve_lista_vacia(): void
    {
        $uc = new VerOrbixData($this->idMatch, $this->resolver, $this->factory);

        $this->assertSame(['lista' => []], $uc('H', 'x'));
    }

    public function test_orbix_lista_las_activas_no_unidas(): void
    {
        $criterio = null;
        $this->repoN->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterio): array {
            $criterio = DescribeCriteria::filtros($c);

            return [$this->persona(10011), $this->persona(10012)];
        });
        $this->idMatch->method('matching')->willReturnCallback(
            fn (Criteria $c): array => in_array('id_orbix eq 10011', DescribeCriteria::filtros($c), true) ? [$this->match(1, 10011)] : []
        );

        $r = (new VerOrbixData($this->idMatch, $this->resolver, $this->factory))('H', 'n');

        $this->assertSame(['situacion eq A'], $criterio);
        $this->assertSame([1], array_keys($r['lista']));
        $this->assertSame(10012, $r['lista'][1]['id_nom_orbix']);
        $this->assertSame('Apellidos 10012', $r['lista'][1]['ape_nom']);
        $this->assertNull($r['lista'][1]['f_nacimiento']);
    }

    public function test_orbix_posibles_bdu_y_contexto_de_union(): void
    {
        $this->sincro->method('posiblesBDU')->with(10011)->willReturn([['id' => 100001]]);

        $r = (new VerOrbixData($this->idMatch, $this->resolver, $this->factory))->getPosiblesMatches('n', 'H', 'dlb', 10011);

        $this->assertSame([['id' => 100001]], $r['posibles_bdu']);
        $this->assertNotSame('', $r['ctx_unir']);
    }

    // ---- otras pantallas ----------------------------------------------------------------------

    public function test_desaparecidos_de_orbix_y_de_listas_tolera_fichas_inexistentes(): void
    {
        $this->bduRepo->method('findById')->willReturnMap([[100001, $this->bdu(100001, 'Gil, Ana', 'dlb')], [100002, null]]);
        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn('Paz, Eva');
        $p->method('getDl')->willReturn('dlp');
        $dlRepo->method('findById')->willReturnMap([[10011, $p], [10012, null]]);

        $deOrbix = (new VerDesaparecidosDeOrbixData($this->bduRepo))('n', [100001, 100002])['personas'];
        $deListas = (new VerDesaparecidosDeListasData($dlRepo))('n', [10011, 10012])['personas'];

        $this->assertSame(['Gil, Ana', 'dlb'], [$deOrbix[1]['ape_nom'], $deOrbix[1]['dl']]);
        $this->assertSame(['', ''], [$deOrbix[2]['ape_nom'], $deOrbix[2]['dl']]);
        $this->assertNotSame('', $deOrbix[1]['ctx_desunir']);
        $this->assertSame(['Paz, Eva', 'dlp'], [$deListas[1]['ape_nom'], $deListas[1]['dl']]);
        $this->assertSame(['', ''], [$deListas[2]['ape_nom'], $deListas[2]['dl']]);
        $this->assertNotSame('', $deListas[2]['ctx_baja']);
    }

    /**
     * @dataProvider dlListasProvider
     */
    public function test_la_dl_de_listas_se_traduce_a_la_de_orbix(string $dlListas, string $esperada): void
    {
        $this->idMatch->method('matching')->willReturn([$this->match(100001, 10011)]);
        $this->bduRepo->method('findById')->willReturn($this->bdu(100001, 'Gil, Ana', $dlListas));

        $r = (new VerOrbixOtraDlData($this->idMatch, $this->bduRepo))('n', [100001])['personas'];

        $this->assertSame($esperada, $r[1]['dl']);
        $this->assertSame(10011, $r[1]['id_nom_orbix']);
        $this->assertNotSame('', $r[1]['ctx_trasladar_a']);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function dlListasProvider(): iterable
    {
        yield 'delegación normal' => ['b', 'dlb'];
        yield 'región con sufijo cr' => ['Ncr', 'N'];
    }

    public function test_otra_dl_salta_los_ids_sin_vinculo_pero_conserva_la_numeracion(): void
    {
        $this->idMatch->method('matching')->willReturnCallback(
            fn (Criteria $c): array => in_array('id_listas eq 100002', DescribeCriteria::filtros($c), true) ? [$this->match(100002, null)] : []
        );
        $this->bduRepo->method('findById')->willReturn(null);

        $r = (new VerOrbixOtraDlData($this->idMatch, $this->bduRepo))('n', [100001, 100002])['personas'];

        $this->assertSame([2], array_keys($r));
        $this->assertSame([0, ''], [$r[2]['id_nom_orbix'], $r[2]['ape_nom']]);
    }

    public function test_traslados_rechaza_tipo_desconocido_y_lista_los_datos_de_cada_persona(): void
    {
        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn('Paz, Eva');
        $p->method('getDl')->willReturn('dlp');
        $dlRepo->method('findById')->willReturn($p);
        $fabrica = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $fabrica->method('createWithConnection')->willReturn($dlRepo);
        $this->sincro->method('buscarEnOrbix')->willReturn('H-dlpv');
        $this->sincro->method('conexion')->willReturn(new \PDO('sqlite::memory:'));
        $this->sincro->expects($this->once())->method('restaurarConexion');
        $uc = new VerTrasladosData($this->resolver, $fabrica, $this->factory);

        $this->assertArrayHasKey('error', $uc('x', [10011]));
        $r = $uc('n', [10011])['personas'];

        $this->assertSame(['Paz, Eva', 'dlp', 'dlp'], [$r[1]['ape_nom'], $r[1]['dl'], $r[1]['dl_actual']]);
        $this->assertNotSame('', $r[1]['ctx_trasladar']);
    }

    // ---- SincroIndexData ----------------------------------------------------------------------

    private function permiso(array $oficinas): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $perm;
    }

    private function indice(?CopiarBDU $copiar = null): SincroIndexData
    {
        $copiar ??= $this->createMock(CopiarBDU::class);
        $copiar->method('ultimaActualizacion')->willReturn('2026-11-10 08:00');

        return new SincroIndexData($this->idMatch, $this->bduRepo, $copiar, $this->resolver, $this->factory);
    }

    public function test_el_indice_exige_encontrar_la_dl_en_listas_y_permiso_sobre_el_tipo(): void
    {
        $this->permiso([MenuDlPermissionBits::SM]);

        $this->sincro->method('dlOrbix2Listas')->willReturnOnConsecutiveCalls(false, 'dlb', 'dlb', 'dlb');
        $this->assertNotSame('', ($this->indice())('n')['error'], 'dl no encontrada');
        $this->assertNotSame('', ($this->indice())('a')['error'], 'sin permiso para agregados');
        $this->assertNotSame('', ($this->indice())('x')['error'], 'tipo desconocido');
    }

    public function test_sin_permisos_en_sesion_no_hay_acceso(): void
    {
        unset($_SESSION['oPerm']);
        $this->sincro->method('dlOrbix2Listas')->willReturn('dlb');

        $this->assertNotSame('', ($this->indice())('n')['error']);
    }

    /**
     * @dataProvider permisosPorTipoProvider
     */
    public function test_cada_tipo_pide_su_propio_permiso(string $tipo, string $oficina): void
    {
        $this->permiso([$oficina]);
        $this->sincro->method('dlOrbix2Listas')->willReturn('dlb');
        $this->bduRepo->method('getPersonaBDUQuery')->willReturn([]);
        $this->repoN->method('matching')->willReturn([]);

        $r = ($this->indice())($tipo);

        // `a`, `s` y `sssc` no tienen repositorio simulado y pueden fallar después del permiso: lo que importa
        // es que no sea el error de permisos.
        $this->assertNotSame(_("no tiene permisos"), $r['error'] ?? '');
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function permisosPorTipoProvider(): iterable
    {
        yield 'numerarios' => ['n', MenuDlPermissionBits::SM];
        yield 'agregados' => ['a', MenuDlPermissionBits::AGD];
        yield 'supernumerarios' => ['s', MenuDlPermissionBits::SG];
        yield 'sssc' => ['sssc', MenuDlPermissionBits::DES];
    }

    public function test_el_indice_clasifica_unidas_traslados_desaparecidas_y_no_unidas(): void
    {
        $this->permiso([MenuDlPermissionBits::SM]);
        $this->sincro->method('dlOrbix2Listas')->willReturn('dlb');
        $consultas = [];
        $this->bduRepo->method('getPersonaBDUQuery')->willReturnCallback(function (string $q) use (&$consultas): array {
            $consultas[] = $q;

            return [$this->bdu(100001), $this->bdu(100002), $this->bdu(100003), $this->bdu(100004), $this->bdu(100005)];
        });
        // 100001 unida y activa; 100002 unida con baja y en otra dl; 100003 unida, sin ficha y sin esquema; 100004 unida sin id; 100005 sin unir.
        $this->idMatch->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = implode('|', DescribeCriteria::filtros($c));
            return match (true) {
                str_contains($f, 'id_listas eq 100001') => [$this->match(100001, 10011)],
                str_contains($f, 'id_listas eq 100002') => [$this->match(100002, 10012)],
                str_contains($f, 'id_listas eq 100003') => [$this->match(100003, 10013)],
                str_contains($f, 'id_listas eq 100004') => [$this->match(100004, null)],
                str_contains($f, 'id_orbix eq 10011') => [$this->match(100001, 10011)],
                default => [],
            };
        });
        $this->repoN->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = implode('|', DescribeCriteria::filtros($c));
            return match (true) {
                str_contains($f, 'id_nom eq 10011') => [$this->persona(10011, 'A')],
                str_contains($f, 'id_nom eq 10012') => [$this->persona(10012, 'B')],
                str_contains($f, 'situacion eq A') => [$this->persona(10011), $this->persona(10020)],
                default => [],
            };
        });
        $this->sincro->method('buscarEnOrbix')->willReturnCallback(fn (int $id): string => $id === 10012 ? 'H-dlpv' : '');
        $this->bduRepo->method('findById')->willReturn($this->bdu(100001, 'Gil, Ana', 'dlb'));

        $r = ($this->indice())('n');

        $this->assertSame(['dlb', 'H'], [$r['dl_listas'], $r['region']]);
        $this->assertSame([1, 1, 1, 1], [$r['p1_unidas_dl'], $r['p2_unidas_otradl'], $r['p3_unidas_desaparecidas'], $r['p456_listas_no_unidas']]);
        $this->assertSame([10012], $r['ids_traslados']);
        $this->assertSame([100003], $r['ids_desaparecidos_de_orbix']);
        $this->assertSame([1], [$r['p910_orbix_no_unidas']]);
        $this->assertStringEndsWith(' UTC', $r['fecha_actualizacion']);
        $this->assertStringContainsString("identif::text LIKE '1%'", $consultas[0]);
        $this->assertStringContainsString("pertenece_r='H'", $consultas[0]);
        $this->assertSame('frontend/dbextern/controller/sincro_index.php', $r['link_spec_self']['path']);
        $this->assertSame(['tipo' => 'n'], $r['link_spec_self']['query']);
        $this->assertSame('[10012]', $r['link_spec_ver_traslados']['query']['ids_traslados']);
        foreach (['ctx_syncro', 'ctx_refrescar'] as $k) {
            $this->assertNotSame('', $r[$k]);
        }
    }

    public function test_orbix_unidas_a_fichas_desaparecidas_o_de_otra_dl_se_cuentan_aparte(): void
    {
        $this->permiso([MenuDlPermissionBits::SM]);
        $this->sincro->method('dlOrbix2Listas')->willReturn('dlb');
        $this->bduRepo->method('getPersonaBDUQuery')->willReturn([]);
        $this->repoN->method('matching')->willReturn([$this->persona(10011), $this->persona(10012), $this->persona(10013), $this->persona(10014)]);
        $this->idMatch->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = implode('|', DescribeCriteria::filtros($c));
            return match (true) {
                str_contains($f, 'id_orbix eq 10011') => [$this->match(200001, 10011)],
                str_contains($f, 'id_orbix eq 10012') => [$this->match(200002, 10012)],
                str_contains($f, 'id_orbix eq 10013') => [$this->match(200003, 10013)],
                default => [],
            };
        });
        // 200001 no existe en la BDU; 200002 existe sin apellidos; 200003 existe en otra dl.
        $this->bduRepo->method('findById')->willReturnMap([
            [200001, null],
            [200002, $this->bdu(200002, '', 'dlb')],
            [200003, $this->bdu(200003, 'Gil, Ana', 'dlx')],
        ]);

        $r = ($this->indice())('n');

        $this->assertSame([2, 1, 1], [$r['p8_orbix_unidas_desaparecidas'], $r['p7_orbix_unidas_otra_dl'], $r['p910_orbix_no_unidas']]);
        $this->assertSame([10011, 10012], $r['ids_desaparecidos_de_listas']);
        $this->assertSame([200003], $r['ids_traslados_A']);
    }

    public function test_las_regiones_con_varias_dl_consultan_cada_una(): void
    {
        $this->permiso([MenuDlPermissionBits::SM]);
        $this->sincro->method('dlOrbix2Listas')->willReturn('dlb');
        $consultas = [];
        $this->bduRepo->method('getPersonaBDUQuery')->willReturnCallback(function (string $q) use (&$consultas): array {
            $consultas[] = $q;

            return [];
        });
        $this->repoN->method('matching')->willReturn([]);

        if (ConfigGlobal::mi_region() !== 'Pla') {
            $this->assertSame(1, $this->contarConsultas($consultas, $this->indice()));
        } else {
            $this->assertSame(1 + count(ConfigGlobal::REGIONES_CON_DL['Pla']), $this->contarConsultas($consultas, $this->indice()));
        }
    }

    /**
     * @param list<string> $consultas
     */
    private function contarConsultas(array &$consultas, SincroIndexData $uc): int
    {
        $uc('n');

        return count($consultas);
    }
}
