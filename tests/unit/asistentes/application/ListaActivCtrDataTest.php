<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\ListaActivCtrData;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\XPermisos;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaSSSC;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Asistentes a actividades por centro: qué centros se listan según el grupo y qué actividades salen por persona. */
final class ListaActivCtrDataTest extends myTest
{
    private CentroDlRepositoryInterface&MockObject $centros;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private PersonaSSSCRepositoryInterface&MockObject $sssc;
    private PersonaDlRepositoryInterface&MockObject $personasDl;
    private AsistenteActividadService&MockObject $asistencias;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<string> */
    private array $filtrosCentros = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oPerm', 'session_auth', 'oConfig'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
        $_SESSION['session_auth'] = ['sfsv' => 1];
        $this->permiso([]);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centros->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosCentros = DescribeCriteria::filtros($c);

            return $this->centrosDevueltos;
        });
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->sssc = $this->createMock(PersonaSSSCRepositoryInterface::class);
        $this->personasDl = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->asistencias = $this->createMock(AsistenteActividadService::class);
    }

    /** @var list<CentroDl> */
    private array $centrosDevueltos = [];

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

    /** @param list<string> $oficinas */
    private function permiso(array $oficinas): void
    {
        $p = $this->createMock(XPermisos::class);
        $p->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $p;
    }

    private function uc(): ListaActivCtrData
    {
        return new ListaActivCtrData($this->centros, $this->actividades, $this->sssc, $this->personasDl, $this->asistencias);
    }

    private function centro(int $id, string $nombre): CentroDl&MockObject
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getId_ubi')->willReturn($id);
        $c->method('getNombre_ubi')->willReturn($nombre);

        return $c;
    }

    private const PERIODO = ['periodo' => 'otro', 'empiezamin' => '01/09/2025', 'empiezamax' => '31/08/2026'];

    /**
     * @dataProvider gruposProvider
     */
    public function test_cada_grupo_filtra_los_centros_por_su_tipo(string $grupo, array $esperados, array $noEsperados = []): void
    {
        $this->uc()->build(['n_agd' => $grupo, 'id_ubi' => '1003'] + self::PERIODO);

        foreach ($esperados as $e) {
            $this->assertContains($e, $this->filtrosCentros);
        }
        foreach ($noEsperados as $n) {
            $this->assertNotContains($n, $this->filtrosCentros);
        }
        $this->assertContains('active eq true', $this->filtrosCentros);
    }

    /**
     * @return iterable<string, array{string, list<string>, list<string>}>
     */
    public static function gruposProvider(): iterable
    {
        yield 'agregados' => ['a', ['tipo_ctr matches ^a'], []];
        yield 'numerarios' => ['n', ['tipo_ctr matches ^n'], []];
        yield 'nm' => ['nm', ['tipo_ctr matches ^nm'], []];
        yield 'nj' => ['nj', ['tipo_ctr matches ^nj(ce)*'], []];
        yield 'sssc' => ['sssc', ['tipo_ctr matches ^ss'], []];
        yield 'un centro' => ['c', ['id_ubi eq 1003'], ['tipo_ctr matches ^n']];
        yield 'sin grupo' => ['', [], ['tipo_ctr matches ^n', 'tipo_ctr matches ^a']];
    }

    public function test_sin_centros_el_resultado_esta_vacio(): void
    {
        $this->assertSame(['aCentros' => []], $this->uc()->build(self::PERIODO));
    }

    private function persona(string $clase, int $id, string $nombre): MockObject
    {
        $p = $this->createMock($clase);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getPrefApellidosNombre')->willReturn($nombre);

        return $p;
    }

    private function asistente(int $idActiv, int|string|null $plaza): Asistente
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_activ')->willReturn($idActiv);
        $a->method('getPlazaVo')->willReturn($plaza === null ? null : new PlazaId((int) $plaza));

        return $a;
    }

    public function test_cada_centro_lista_a_sus_personas_con_sus_actividades_y_la_plaza_no_asignada_se_marca(): void
    {
        $this->centrosDevueltos = [$this->centro(1003, 'Centro A')];
        $this->personasDl->method('matching')->willReturn([$this->persona(PersonaDl::class, 10011, 'Gil, Ana'), $this->persona(PersonaDl::class, 10012, 'Paz, Eva')]);
        $this->asistencias->method('getActividadesDeAsistente')->willReturnCallback(fn (): array => [$this->asistente(3001, PlazaId::ASIGNADA), $this->asistente(3002, 1), $this->asistente(3003, PlazaId::ASIGNADA)]);
        $this->actividades->method('findById')->willReturnCallback(function (int $id): ?ActividadAll {
            if ($id === 3003) {
                return null;
            }
            $a = $this->createMock(ActividadAll::class);
            $a->method('getNom_activ')->willReturn("act $id");

            return $a;
        });

        $r = $this->uc()->build(['n_agd' => 'n'] + self::PERIODO);

        $this->assertSame('Centro A', $r['aCentros'][1003]['nombre_ubi']);
        $this->assertSame([1, 2], array_keys($r['aCentros'][1003]['personas']));
        $actividades = $r['aCentros'][1003]['personas'][1]['actividades'];
        $this->assertCount(2, $actividades, 'la actividad inexistente se omite');
        $this->assertSame('act 3001', $actividades[0]);
        $this->assertStringStartsWith('act 3002<span class=alert>', $actividades[1]);
    }

    public function test_una_persona_sin_asistencias_tiene_lista_vacia(): void
    {
        $this->centrosDevueltos = [$this->centro(1003, 'Centro A')];
        $this->personasDl->method('matching')->willReturn([$this->persona(PersonaDl::class, 10011, 'Gil, Ana')]);
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([]);

        $this->assertSame([], $this->uc()->build(self::PERIODO)['aCentros'][1003]['personas'][1]['actividades']);
    }

    public function test_el_grupo_sssc_usa_su_propio_repositorio_de_personas(): void
    {
        $this->centrosDevueltos = [$this->centro(1003, 'Centro A')];
        $this->sssc->expects($this->once())->method('matching')->willReturn([$this->persona(PersonaSSSC::class, 10011, 'Gil, Ana')]);
        $this->personasDl->expects($this->never())->method('matching');
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([]);

        $r = $this->uc()->build(['n_agd' => 'sssc'] + self::PERIODO);

        $this->assertSame('Gil, Ana', $r['aCentros'][1003]['personas'][1]['ap_nom']);
    }

    public function test_el_filtro_de_actividades_incluye_el_tipo_y_el_periodo_pedido(): void
    {
        $this->centrosDevueltos = [$this->centro(1003, 'Centro A')];
        $this->personasDl->method('matching')->willReturn([$this->persona(PersonaDl::class, 10011, 'Gil, Ana')]);
        $filtros = [];
        $this->asistencias->method('getActividadesDeAsistente')->willReturnCallback(function (Criteria $asist, Criteria $act) use (&$filtros): array {
            $filtros = [DescribeCriteria::filtros($asist), DescribeCriteria::filtros($act)];

            return [];
        });

        $this->uc()->build(['sasistentes' => 'n', 'sactividad' => 'crt'] + self::PERIODO);

        $this->assertSame(['id_nom eq 10011', 'propio eq true'], $filtros[0]);
        $this->assertStringStartsWith('id_tipo_activ matches ^', $filtros[1][0]);
        $this->assertStringContainsString('|^', $filtros[1][0], 'crt de n incluye los de agd y los de sr');
        $this->assertStringContainsString('between', $filtros[1][1]);
        $this->assertStringContainsString('2025-09-01', $filtros[1][1]);
    }

    /**
     * @dataProvider sfsvProvider
     * @param list<string> $oficinas
     */
    public function test_sv_solo_ve_sf_con_permiso_y_sf_siempre_ve_sf(int $sfsv, string $pedido, array $oficinas, string $esperado): void
    {
        $_SESSION['session_auth'] = ['sfsv' => $sfsv];
        $this->permiso($oficinas);
        $this->centrosDevueltos = [$this->centro(1003, 'Centro A')];
        $this->personasDl->method('matching')->willReturn([$this->persona(PersonaDl::class, 10011, 'Gil, Ana')]);
        $tipo = '';
        $this->asistencias->method('getActividadesDeAsistente')->willReturnCallback(function (Criteria $asist, Criteria $act) use (&$tipo): array {
            $tipo = DescribeCriteria::filtros($act)[0];

            return [];
        });

        $this->uc()->build(['ssfsv' => $pedido, 'sasistentes' => 'n', 'sactividad' => 'ca'] + self::PERIODO);

        $this->assertStringStartsWith("id_tipo_activ matches ^$esperado", $tipo);
    }

    /**
     * @return iterable<string, array{int, string, list<string>, string}>
     */
    public static function sfsvProvider(): iterable
    {
        yield 'sv sin permiso pide sf: sigue en sv' => [1, 'sf', [], '1'];
        yield 'sv con des pide sf: ve sf' => [1, 'sf', [MenuDlPermissionBits::DES], '2'];
        yield 'sv con vcsd pide sf' => [1, 'sf', [MenuDlPermissionBits::VCSD], '2'];
        yield 'sf siempre ve sf' => [2, 'sv', [], '2'];
    }
}
