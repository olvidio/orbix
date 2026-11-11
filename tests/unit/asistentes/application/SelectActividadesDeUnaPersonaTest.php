<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\asistentes\application\Select_actividades_de_una_persona;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\dossiers\application\PermDossier;
use src\permisos\domain\XPermisos;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Widget del dossier 1301: actividades a las que asiste una persona. */
final class SelectActividadesDeUnaPersonaTest extends myTest
{
    private AsistenteActividadService&MockObject $asistencias;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private PersonaFinderService&MockObject $finder;
    /** @var array<string, mixed> */
    private array $anterior = [];
    private bool $hayFilas = true;

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oConfig', 'oPerm', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        $this->permiso([]);
        $this->asistencias = $this->createMock(AsistenteActividadService::class);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->finder = $this->createMock(PersonaFinderService::class);
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

    /** @param list<string> $oficinas */
    private function permiso(array $oficinas): void
    {
        $p = $this->createMock(XPermisos::class);
        $p->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $p;
    }

    private function widget(): Select_actividades_de_una_persona
    {
        $w = new Select_actividades_de_una_persona($this->asistencias, $this->actividades, $this->finder);
        $w->setPau('p');
        $w->setObj_pau('PersonaN');
        $w->setId_pau(10011);
        $w->setId_dossier(1301);
        $w->setBloque('bloque1');
        $w->setQueSel('sel');
        $w->setStackActual(3);

        return $w;
    }

    private function personaEncontrada(string $idTabla = 'n'): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_tabla')->willReturn($idTabla);
        $this->finder->method('findPersonaEnGlobalODePaso')->with(10011)->willReturn($p);
    }

    private function asistencia(int $idActiv, bool $propio = true, bool $falta = false, bool $estOk = true): Asistente
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_activ')->willReturn($idActiv);
        $a->method('isPropio')->willReturn($propio);
        $a->method('isFalta')->willReturn($falta);
        $a->method('isEst_ok')->willReturn($estOk);
        $a->method('getObserv')->willReturn('nota');

        return $a;
    }

    private function actividad(string $nombre, int $tipo = 112001): ActividadAll
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn($nombre);
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $a->method('getF_ini')->willReturn(new DateTimeLocal('2026-02-01'));
        $a->method('getF_fin')->willReturn(new DateTimeLocal('2026-02-10'));

        return $a;
    }

    public function test_si_no_se_encuentra_a_la_persona_hay_mensaje_de_error_y_tabla_vacia(): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn(null);

        $s = $this->widget()->getSegmentData();

        $this->assertStringContainsString('10011', $s['msg_err']);
        $this->assertSame([], $s['tabla']['valores']);
        $this->assertSame([], $s['links_dl_specs']);
    }

    public function test_el_segmento_declara_su_tipo_cabeceras_botones_y_hash(): void
    {
        $this->personaEncontrada();
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([]);

        $s = $this->widget()->getSegmentData();

        $this->assertSame('select_actividades_de_una_persona', $s['segment_tipo']);
        $this->assertCount(6, $s['tabla']['cabeceras']);
        $this->assertCount(2, $s['tabla']['botones']);
        $this->assertSame('src/asistentes/asistente_eliminar', $s['wrapper']['url_eliminar_path']);
        $this->assertSame('bloque1', $s['wrapper']['bloque']);
        $this->assertSame(['pau' => 'p', 'id_pau' => 10011, 'obj_pau' => 'PersonaN', 'queSel' => 'sel', 'id_dossier' => 1301, 'permiso' => 3, 'stack' => 3], $s['hash']['campos_hidden']);
        $this->assertSame('modo_curso', $s['hash']['campos_form']);
    }

    /**
     * @dataProvider modosProvider
     * @param list<string> $esperados
     * @param list<string> $noEsperados
     */
    public function test_el_modo_de_curso_decide_si_se_filtra_por_estado_y_fechas(int|string|null $modo, array $esperados, array $noEsperados, int $orden): void
    {
        $this->personaEncontrada();
        $filtros = [];
        $this->asistencias->method('getActividadesDeAsistente')->willReturnCallback(function (Criteria $asist, Criteria $act, bool $reverse) use (&$filtros): array {
            $filtros = DescribeCriteria::filtros($act);

            return [];
        });
        $w = $this->widget();
        $w->setModo_curso($modo);

        $w->getSegmentData();

        foreach ($esperados as $e) {
            $this->assertNotSame([], preg_grep('/^' . preg_quote($e, '/') . '/', $filtros), $e);
        }
        foreach ($noEsperados as $n) {
            $this->assertSame([], preg_grep('/^' . preg_quote($n, '/') . '/', $filtros), $n);
        }
        $this->assertCount($orden, $filtros);
    }

    /**
     * @return iterable<string, array{int|string|null, list<string>, list<string>, int}>
     */
    public static function modosProvider(): iterable
    {
        yield 'por defecto: actuales del curso' => [null, ['status eq', 'f_ini between'], [], 2];
        yield 'cero equivale a 1' => [0, ['status eq', 'f_ini between'], [], 2];
        yield 'todas las del curso' => ['2', ['f_ini between'], ['status'], 1];
        yield 'sin filtro' => [3, [], ['status', 'f_ini'], 0];
    }

    public function test_cada_actividad_es_una_fila_con_fechas_nombre_y_marcas_si_no(): void
    {
        $this->personaEncontrada();
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([$this->asistencia(3001, true, false, true), $this->asistencia(3002, false, true, false), $this->asistencia(3003)]);
        $this->actividades->method('findById')->willReturnCallback(fn (int $id): ?ActividadAll => $id === 3003 ? null : $this->actividad("act $id"));
        $this->permiso([\src\permisos\domain\MenuDlPermissionBits::DES]);

        $valores = $this->widget()->getSegmentData()['tabla']['valores'];

        $this->assertSame([1, 2], array_keys($valores));
        $this->assertSame(['1/2/2026-10/2/2026', 'act 3001', 'si', 'si', 'no', 'nota'], [$valores[1][1], $valores[1][2], $valores[1][3], $valores[1][4], $valores[1][5], $valores[1][6]]);
        $this->assertSame(['no', 'no', 'si'], [$valores[2][3], $valores[2][4], $valores[2][5]]);
    }

    public function test_el_selector_solo_existe_si_hay_permiso_sobre_ese_tipo_de_actividad(): void
    {
        $this->personaEncontrada();
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([$this->asistencia(3001)]);
        $this->actividades->method('findById')->willReturn($this->actividad('act', 112001));
        $this->permiso([\src\permisos\domain\MenuDlPermissionBits::DES]);
        $ref = (new PermDossier())->perm_activ_pers('n');
        $conPermiso = !empty($ref['112']['perm'] ?? null);

        $fila = $this->widget()->getSegmentData()['tabla']['valores'][1];

        $this->assertSame($conPermiso ? '3001' : '', $fila['sel']);
    }

    public function test_el_nombre_de_actividades_de_otro_sfsv_se_oculta_salvo_con_permiso_des(): void
    {
        $this->personaEncontrada();
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([$this->asistencia(3001)]);
        // Tipo 2xxxxx = sf; la sesión es sv.
        $this->actividades->method('findById')->willReturn($this->actividad('Nombre privado', 212001));

        $oculto = $this->widget()->getSegmentData()['tabla']['valores'][1][2];
        $this->permiso([\src\permisos\domain\MenuDlPermissionBits::DES]);
        $visible = $this->widget()->getSegmentData()['tabla']['valores'][1][2];

        $this->assertNotSame('Nombre privado', $oculto);
        $this->assertSame('Nombre privado', $visible);
    }

    public function test_la_seleccion_y_el_scroll_solo_se_restauran_si_hay_filas(): void
    {
        $this->personaEncontrada();
        $this->asistencias->method('getActividadesDeAsistente')->willReturnCallback(fn (): array => $this->hayFilas ? [$this->asistencia(3001)] : []);
        $this->actividades->method('findById')->willReturn($this->actividad('act'));
        $conFilas = $this->widget();
        $conFilas->setQid_sel('3001');
        $conFilas->setQscroll_id(250);
        $sinFilas = $this->widget();
        $sinFilas->setQid_sel('3001');

        $this->hayFilas = true;
        $a = $conFilas->getSegmentData()['tabla']['valores'];
        $this->hayFilas = false;
        $b = $sinFilas->getSegmentData()['tabla']['valores'];

        $this->assertSame(['3001', 250], [$a['select'], $a['scroll_id']]);
        $this->assertSame([], $b);
    }

    public function test_los_enlaces_de_alta_solo_salen_para_los_tipos_con_permiso(): void
    {
        $this->personaEncontrada();
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([]);
        $this->permiso([\src\permisos\domain\MenuDlPermissionBits::DES]);
        $ref = (new PermDossier())->perm_activ_pers('n');
        $esperados = count(array_filter($ref, static fn (array $v): bool => !empty($v['perm'])));

        $s = $this->widget()->getSegmentData();

        $this->assertLessThanOrEqual($esperados, count($s['links_dl_specs']));
        $this->assertLessThanOrEqual($esperados, count($s['links_otros_specs']));
        foreach ($s['links_dl_specs'] as $spec) {
            $this->assertSame('nuevo', $spec['query']['mod']);
            $this->assertSame(\src\shared\config\ConfigGlobal::mi_delef(), $spec['query']['que_dl']);
        }
        foreach ($s['links_otros_specs'] as $spec) {
            $this->assertArrayNotHasKey('que_dl', $spec['query']);
        }
    }

    public function test_los_getters_devuelven_lo_fijado(): void
    {
        $w = $this->widget();
        $w->setPermiso('2');
        $w->setStatus('x');

        $this->assertSame([1301, 'p', 'PersonaN', 10011, 2, 'x'], [$w->getId_dossier(), $w->getPau(), $w->getObj_pau(), $w->getId_pau(), $w->getPermiso(), $w->getStatus()]);
    }
}
