<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\domain\value_objects\CargoCode;
use src\actividadcargos\domain\value_objects\TipoCargoCode;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\Select_asistentes_a_una_actividad;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\dossiers\application\DossierTipoPublicUrls;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\XPermisos;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\services\TelecoPersonaService;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Widget del dossier 3101 (asistentes de una actividad): cargos primero y después asistentes, numeración, columnas,
 * botones según apps y ámbito, título de plazas, enlaces para añadir y resumen de plazas. Sobre `getSegmentData()`,
 * que es lo que pinta la pantalla. El recuento fino de plazas pedidas/asignadas está en
 * `SelectAsistentesAUnaActividadPlazasPedidasTest`.
 *
 * En la sesión de `myTest` mi dl es «dlb».
 */
final class SelectAsistentesAUnaActividadTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_ACTIV = 3001;
    private const TIPO_CA_N = 112025;
    private const TIPO_CRT_S = 141025;

    private ActividadAllRepositoryInterface&MockObject $actividades;
    private ResumenPlazasService&MockObject $resumen;
    private AsistenteRepositoryInterface&MockObject $asistentes;
    private ActividadCargoRepositoryInterface&MockObject $actividadCargos;
    private CargoRepositoryInterface&MockObject $cargos;
    private PersonaFinderService&MockObject $finder;
    private TelecoPersonaService&MockObject $telecos;
    /** @var array<int, PersonaDl> */
    private array $personas = [];
    /** @var list<Asistente> */
    private array $asistentesActividad = [];
    /** @var list<ActividadCargo> */
    private array $cargosActividad = [];
    /** @var array<int, array<string, string>> */
    private array $telecosPorPersona = [];
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'session_auth', 'oConfig', 'oPerm'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $this->ambito('dl');
        $this->apps(['asistentes']);
        $this->oficinas([MenuDlPermissionBits::SM]);

        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->resumen = $this->createMock(ResumenPlazasService::class);
        $this->resumen->method('getResumen')->willReturn([]);
        $this->asistentes = $this->createMock(AsistenteRepositoryInterface::class);
        $this->asistentes->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            if (count($f) === 1) {
                return $this->asistentesActividad;
            }
            $idNom = (int) substr($f[1], strlen('id_nom eq '));

            return array_values(array_filter($this->asistentesActividad, static fn (Asistente $a): bool => $a->getId_nom() === $idNom));
        });
        $this->actividadCargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->actividadCargos->method('matching')->willReturnCallback(fn (): array => $this->cargosActividad);
        $this->cargos = $this->createMock(CargoRepositoryInterface::class);
        $this->cargos->method('findById')->willReturnCallback(function (int $id): ?Cargo {
            $def = [1 => ['d.est.', 'd.est.'], 2 => ['sacd', 'sacd']][$id] ?? null;
            if ($def === null) {
                return null;
            }
            $c = $this->createMock(Cargo::class);
            $c->method('getCargoVo')->willReturn(new CargoCode($def[0]));
            $c->method('getTipoCargoVo')->willReturn(new TipoCargoCode($def[1]));

            return $c;
        });
        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->finder->method('findPersonaParaListado')->willReturnCallback(fn (int $id): ?PersonaDl => $this->personas[$id] ?? null);
        $this->finder->method('findPersonaEnDl')->willReturnCallback(fn (int $id): ?PersonaDl => $this->personas[$id] ?? null);
        $this->telecos = $this->createMock(TelecoPersonaService::class);
        $this->telecos->method('getTelecosPorTipo')->willReturnCallback(
            fn (int $id, string $tipo): string => $this->telecosPorPersona[$id][$tipo] ?? ''
        );
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

    private function ambito(string $ambito): void
    {
        $curso = ['ini_dia' => 1, 'ini_mes' => 10, 'fin_dia' => 30, 'fin_mes' => 9];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, $ambito, null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
    }

    /** @param list<string> $instaladas */
    private function apps(array $instaladas): void
    {
        $todas = ['asistentes' => 961, 'actividadcargos' => 962, 'actividadplazas' => 963, 'actividadestudios' => 964];
        $config = is_array($_SESSION['config'] ?? null) ? $_SESSION['config'] : [];
        $config['a_apps'] = $todas;
        $config['app_installed'] = array_values(array_intersect_key($todas, array_flip($instaladas)));
        $_SESSION['config'] = $config;
    }

    /** @param list<string> $oficinas */
    private function oficinas(array $oficinas): void
    {
        $p = $this->createMock(XPermisos::class);
        $p->method('have_perm_oficina')->willReturnCallback(static fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $p;
    }

    private function actividad(int $tipo = self::TIPO_CA_N, string $dlOrg = 'dlb', ?int $plazas = 30, int $idUbi = 1077): void
    {
        $a = new ActividadAll();
        $a->setId_activ(self::ID_ACTIV);
        $a->setId_tipo_activ($tipo);
        $a->setDl_org($dlOrg);
        $a->setPlazas($plazas);
        $a->setId_ubi($idUbi);
        $this->actividades->method('findById')->willReturn($a);
    }

    private function widget(int $permiso = 3): Select_asistentes_a_una_actividad
    {
        $w = new Select_asistentes_a_una_actividad(
            $this->actividades,
            $this->resumen,
            $this->asistentes,
            $this->actividadCargos,
            $this->cargos,
            $this->finder,
            $this->telecos,
        );
        $w->setPau('a');
        $w->setObj_pau('Actividad');
        $w->setId_dossier(3101);
        $w->setPermiso($permiso);
        $w->setId_pau((string) self::ID_ACTIV);

        return $w;
    }

    private function persona(int $id, string $apellidos, bool $sacd = false, string $idTabla = 'n'): PersonaDl
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn($apellidos);
        $p->method('getNom')->willReturn('Nom' . $id);
        $p->method('getApellidos')->willReturn($apellidos);
        $p->method('isSacd')->willReturn($sacd);
        $p->method('getDl')->willReturn('dlb');
        $p->method('getCentro_o_dl')->willReturn('ctr' . $id);
        $p->method('getId_tabla')->willReturn($idTabla);
        $p->method('getClassName')->willReturn('PersonaDl');
        $this->personas[$id] = $p;

        return $p;
    }

    private function asistente(int $idNom, bool $propio = true, ?int $plaza = null, ?string $propietario = null, string $observ = ''): Asistente
    {
        $a = new Asistente();
        $a->setId_activ(self::ID_ACTIV);
        $a->setId_nom($idNom);
        $a->setPropio($propio);
        $a->setPlaza($plaza);
        $a->setPropietario($propietario);
        $a->setObserv($observ);
        $a->setDl_responsable('dlb');
        $this->asistentesActividad[] = $a;

        return $a;
    }

    private function cargo(int $idNom, int $idCargo, int $idItem, string $observ = ''): void
    {
        $c = $this->createMock(ActividadCargo::class);
        $c->method('getId_nom')->willReturn($idNom);
        $c->method('getId_cargo')->willReturn($idCargo);
        $c->method('getId_item')->willReturn($idItem);
        $c->method('getId_schema')->willReturn(1001);
        $c->method('getObserv')->willReturn($observ);
        $this->cargosActividad[] = $c;
    }

    // ------------------------------------------------------------------ asistentes sin cargos ni plazas

    public function test_los_asistentes_salen_numerados_por_orden_alfabetico_con_todas_sus_columnas(): void
    {
        $this->actividad();
        $this->persona(10011, 'Zamora, Luis', true);
        $this->persona(10012, 'Álvarez, Juan');
        $this->asistente(10011, false, null, null, 'llega tarde');
        $this->asistente(10012);
        $this->telecosPorPersona[10011] = ['telf' => '93 111', 'móvil' => '600 222', 'e-mail' => 'luis@example.org'];
        $this->telecosPorPersona[10012] = ['móvil' => '600 333'];

        $d = $this->widget()->getSegmentData();

        $this->assertSame('select_asistentes_a_una_actividad', $d['segment_tipo']);
        $this->assertSame('', $d['msg_err']);
        $valores = $d['tabla']['valores'];
        $this->assertSame([1, 2], array_keys($valores));
        $this->assertSame([
            'sel' => '10012',
            'clase' => 'plaza2',
            2 => 'Álvarez, Juan  (ctr10012)',
            3 => 'dlb',
            4 => 'sí',
            5 => 'no',
            6 => 'no',
            7 => null,
            8 => '',
            9 => '600 333',
            10 => '',
            11 => 'Nom10012',
            12 => 'Álvarez, Juan',
            13 => 'ctr10012',
            1 => '1.-',
        ], $valores[1]);
        $zamora = $valores[2];
        $this->assertSame(['2.-', 'no', 'llega tarde', 'sí', '93 111 / 600 222', 'luis@example.org'], [$zamora[1], $zamora[4], $zamora[7], $zamora[8], $zamora[9], $zamora[10]]);
    }

    public function test_sin_permiso_de_modificar_las_filas_no_se_pueden_seleccionar(): void
    {
        $this->actividad();
        $this->persona(10011, 'Pla');
        $this->asistente(10011);

        $this->assertSame('', $this->widget(1)->getSegmentData()['tabla']['valores'][1]['sel']);
    }

    public function test_una_persona_que_no_se_encuentra_se_avisa_solo_si_la_actividad_es_de_mi_dl(): void
    {
        $this->actividad();
        $this->asistente(10019);

        $d = $this->widget()->getSegmentData();

        $this->assertStringContainsString('10019', $d['msg_err']);
        $this->assertSame([], $d['tabla']['valores']);
    }

    public function test_en_una_actividad_de_otra_dl_las_personas_se_buscan_en_mi_dl_y_las_ajenas_no_se_avisan(): void
    {
        $this->actividad(self::TIPO_CA_N, 'dlp');
        $this->persona(10011, 'Pla');
        $this->asistente(10011);
        $this->asistente(10019);
        $this->finder->expects($this->never())->method('findPersonaParaListado');

        $d = $this->widget()->getSegmentData();

        $this->assertSame('', $d['msg_err']);
        $this->assertCount(1, $d['tabla']['valores']);
    }

    public function test_la_seleccion_y_el_scroll_se_anyaden_si_hay_filas(): void
    {
        $this->actividad();
        $this->persona(10011, 'Pla');
        $this->asistente(10011);
        $w = $this->widget();
        $w->setQid_sel('10011');
        $w->setQscroll_id(3);

        $d = $w->getSegmentData();

        $this->assertSame(['10011', 3], [$d['tabla']['valores']['select'], $d['tabla']['valores']['scroll_id']]);
        $this->assertSame('10011', $d['id_sel_value']);
    }

    // ------------------------------------------------------------------ botones, cabeceras, enlaces de la pantalla

    /**
     * @return iterable<string, array{list<string>, string, int}>
     */
    public static function botonesProvider(): iterable
    {
        yield 'solo asistentes' => [['asistentes'], 'dl', 4];
        yield 'asistentes y cargos' => [['asistentes', 'actividadcargos'], 'dl', 7];
        yield 'todas' => [['asistentes', 'actividadcargos', 'actividadestudios'], 'dl', 9];
        yield 'región del stgr: solo estudios' => [['asistentes', 'actividadcargos', 'actividadestudios'], 'rstgr', 2];
        yield 'ninguna' => [[], 'dl', 0];
    }

    #[DataProvider('botonesProvider')]
    public function test_los_botones_dependen_de_las_apps_y_del_ambito(array $apps, string $ambito, int $num): void
    {
        $this->apps($apps);
        $this->ambito($ambito);
        $this->actividad();

        $this->assertCount($num, $this->widget()->getSegmentData()['tabla']['botones']);
    }

    public function test_con_plazas_hay_una_columna_mas_con_el_propietario(): void
    {
        $this->actividad();
        $this->assertCount(13, $this->widget()->getSegmentData()['tabla']['cabeceras']);

        $this->apps(['asistentes', 'actividadplazas']);
        $this->assertCount(14, $this->widget()->getSegmentData()['tabla']['cabeceras']);
    }

    public function test_datos_fijos_del_segmento(): void
    {
        $this->actividad();
        $w = $this->widget();
        $w->setBloque('b3101');
        $w->setQueSel('asistentes');

        $d = $w->getSegmentData();

        $this->assertSame(self::ID_ACTIV, $d['id_pau']);
        $this->assertSame('b3101', $d['wrapper']['bloque']);
        $this->assertSame(DossierTipoPublicUrls::relativeFormController(3101), $d['wrapper']['url_form_relative']);
        $this->assertSame(DossierTipoPublicUrls::relativeFormController(3102), $d['wrapper']['url_form_cargos_relative']);
        $this->assertSame('src/asistentes/asistente_eliminar', $d['wrapper']['url_eliminar_path']);
        $this->assertSame(
            ['pau' => 'a', 'id_pau' => self::ID_ACTIV, 'obj_pau' => 'Actividad', 'id_dossier' => 3101, 'queSel' => 'asistentes', 'permiso' => 3],
            $d['hash_main']['campos_hidden']
        );
        $this->assertSame(self::ID_ACTIV, $d['hash_matriculas']['campos_hidden']['id_activ']);
        $this->assertFalse($d['plazas_installed']);
    }

    // ------------------------------------------------------------------ título de plazas

    public function test_el_titulo_de_plazas_es_el_de_la_actividad_o_si_no_el_de_la_casa(): void
    {
        $this->actividad(self::TIPO_CA_N, 'dlb', 30);
        $this->assertStringEndsWith(': 30', $this->widget()->getSegmentData()['plazas_txt']);
    }

    public function test_sin_plazas_en_la_actividad_se_muestran_las_de_la_casa(): void
    {
        $this->actividad(self::TIPO_CA_N, 'dlb', null, 1077);
        $centros = $this->createMock(CentroRepositoryInterface::class);
        $centros->method('findById')->willReturn(null);
        $this->sustituirServicio(CentroRepositoryInterface::class, $centros);
        $casa = $this->createMock(Casa::class);
        $casa->method('getPlazas')->willReturn(40);
        $casa->method('getPlazas_min')->willReturn(25);
        $casas = $this->createMock(CasaRepositoryInterface::class);
        $casas->method('findById')->willReturnCallback(static fn (int $id): ?Casa => $id === 1077 ? $casa : null);
        $this->sustituirServicio(CasaRepositoryInterface::class, $casas);

        $this->assertStringEndsWith(': 40 - 25', $this->widget()->getSegmentData()['plazas_txt']);

        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->actividad(self::TIPO_CA_N, 'dlb', null, 1999);
        $this->assertStringEndsWith(': ?', $this->widget()->getSegmentData()['plazas_txt']);
    }

    // ------------------------------------------------------------------ cargos

    public function test_los_cargos_salen_primero_y_sus_personas_no_se_repiten_entre_los_asistentes(): void
    {
        $this->apps(['asistentes', 'actividadcargos']);
        $this->actividad();
        $this->persona(20001, 'Vidal, Pere');
        $this->persona(20002, 'Soler, Marc', true);
        $this->persona(10011, 'Pla, Joan');
        $this->cargo(20001, 1, 501, 'coordina');
        $this->cargo(20002, 2, 502);
        $this->asistente(20001, true, null, null, 'propio');
        $this->asistente(10011);

        $valores = $this->widget()->getSegmentData()['tabla']['valores'];

        $this->assertSame([1, 2, 3], array_keys($valores));
        $this->assertSame(['d.est.', 'Vidal, Pere  (ctr20001)', 'coordina propio', '20001#501#1#1001'], [$valores[1][1], $valores[1][2], $valores[1][7], $valores[1]['sel']]);
        $this->assertSame('sí', $valores[1][4]);
        $this->assertSame(['sacd', ['span' => 3, 'valor' => 'no asiste']], [$valores[2][1], $valores[2][4]]);
        $this->assertSame(['3.-', 'Pla, Joan  (ctr10011)'], [$valores[3][1], $valores[3][2]], 'la numeración sigue a las filas de cargos');
    }

    /**
     * Las filas de cargos no llevan número, pero cuentan: el primer asistente lleva el número siguiente al de filas
     * de cargos (el total de la lista es el de asistentes de la actividad).
     */
    public function test_con_cargos_el_primer_asistente_se_numera_a_partir_de_las_filas_de_cargos(): void
    {
        $this->apps(['asistentes', 'actividadcargos']);
        $this->actividad();
        $this->persona(20001, 'Vidal, Pere');
        $this->persona(10011, 'Pla, Joan');
        $this->cargo(20001, 1, 501);
        $this->asistente(10011);

        $valores = $this->widget()->getSegmentData()['tabla']['valores'];

        $this->assertSame('2.-', $valores[2][1]);
    }

    public function test_en_actividades_de_s_con_des_o_vcsd_quitar_el_cargo_borra_tambien_la_asistencia(): void
    {
        $this->apps(['asistentes', 'actividadcargos']);
        $this->oficinas([MenuDlPermissionBits::DES]);
        $this->actividad(self::TIPO_CRT_S);
        $this->persona(20001, 'Vidal, Pere');
        $this->cargo(20001, 1, 501);
        $this->asistente(20001);

        $this->assertSame('20001#501#2#1001', $this->widget()->getSegmentData()['tabla']['valores'][1]['sel']);
    }

    public function test_en_sf_el_sacd_no_sale(): void
    {
        $this->apps(['asistentes', 'actividadcargos']);
        $_SESSION['session_auth']['sfsv'] = 2;
        $this->actividad();
        $this->persona(20002, 'Soler, Marc', true);
        $this->cargo(20002, 2, 502);
        $this->asistente(20002);

        $this->assertSame([], $this->widget()->getSegmentData()['tabla']['valores']);
    }

    public function test_dos_filas_de_asistencia_de_la_misma_persona_con_cargo_lanzan_excepcion(): void
    {
        $this->apps(['asistentes', 'actividadcargos']);
        $this->actividad();
        $this->persona(20001, 'Vidal, Pere');
        $this->cargo(20001, 1, 501);
        $this->asistente(20001);
        $this->asistente(20001);

        $this->expectException(\RuntimeException::class);

        $this->widget()->getSegmentData();
    }

    // ------------------------------------------------------------------ enlaces para añadir asistentes

    public function test_con_cargos_se_ofrecen_los_enlaces_para_anyadir_segun_el_tipo_de_actividad(): void
    {
        $this->apps(['asistentes', 'actividadcargos']);
        $this->actividad();

        $d = $this->widget()->getSegmentData();

        $this->assertArrayHasKey('n', $d['links_dl_specs']);
        $this->assertSame('dlb', $d['links_dl_specs']['n']['query']['que_dl']);
        $this->assertSame('PersonaN', $d['links_dl_specs']['n']['query']['obj_pau']);
        $this->assertArrayHasKey('n de paso', $d['links_otros_specs']);
        $this->assertArrayNotHasKey('que_dl', $d['links_otros_specs']['n de paso']['query']);
    }

    public function test_en_la_region_del_stgr_o_sin_permiso_no_hay_enlaces_para_anyadir(): void
    {
        $this->apps(['asistentes', 'actividadcargos']);
        $this->actividad();
        $this->assertSame([], $this->widget(1)->getSegmentData()['links_dl_specs']);

        $this->ambito('rstgr');
        $this->assertSame([], $this->widget()->getSegmentData()['links_dl_specs']);
    }

    /**
     * Hallazgo: los permisos para añadir personas (`a_ref_perm`) solo se calculan dentro de `getCargos()`, que
     * solo se ejecuta con la app `actividadcargos`. Sin esa app no se ofrece ningún enlace para añadir asistentes
     * desde este dossier.
     */
    public function test_sin_la_app_de_cargos_no_se_ofrecen_enlaces_para_anyadir_asistentes(): void
    {
        $this->actividad();

        $d = $this->widget()->getSegmentData();

        $this->assertSame([], $d['links_dl_specs']);
        $this->assertSame([], $d['links_otros_specs']);
    }

    /**
     * Hallazgo: `getCargos()` sobrescribe el permiso del widget con el de la última persona con cargo (según su
     * tipo y los permisos del dossier); ese permiso decide después si los asistentes se pueden seleccionar.
     */
    public function test_el_permiso_de_la_ultima_persona_con_cargo_decide_si_se_seleccionan_los_asistentes(): void
    {
        $this->apps(['asistentes', 'actividadcargos']);
        $this->actividad();
        $this->persona(20001, 'Vidal, Pere', false, 'a');
        $this->persona(10011, 'Pla, Joan');
        $this->cargo(20001, 1, 501);
        $this->asistente(10011);

        $w = $this->widget(3);
        $valores = $w->getSegmentData()['tabla']['valores'];

        $this->assertSame(1, $w->getPermiso(), 'un agd en un ca de n: sin permiso');
        $this->assertSame('', $valores[2]['sel']);
    }

    // ------------------------------------------------------------------ plazas

    public function test_con_plazas_solo_se_numeran_las_asignadas_o_confirmadas(): void
    {
        $this->apps(['asistentes', 'actividadplazas']);
        $this->actividad();
        $this->persona(10011, 'A');
        $this->persona(10012, 'B');
        $this->persona(10013, 'C');
        $this->asistente(10011, true, PlazaId::ASIGNADA, 'dlb>dlb');
        $this->asistente(10012, true, PlazaId::PEDIDA, 'dlb>dlb');
        $this->asistente(10013, true, PlazaId::CONFIRMADA, 'dlb>dlb');

        $d = $this->widget()->getSegmentData();

        $valores = $d['tabla']['valores'];
        $this->assertSame(['1.-', '-', '2.-'], array_column($valores, 1));
        $this->assertSame(['plaza4', 'plaza1', 'plaza5'], array_column($valores, 'clase'));
        $this->assertSame('dlb>dlb', $valores[1][14]);
        $this->assertTrue($d['plazas_installed']);
        $this->assertStringContainsString("fnjs_cmb_plaza('#frm_asistentes_a_una_actividad','4')", $d['leyenda_html']);
        $this->assertStringNotContainsString("'3')", $d['leyenda_html'], 'denegada no se ofrece');
    }

    public function test_un_asistente_sin_propietario_se_avisa(): void
    {
        $this->apps(['asistentes', 'actividadplazas']);
        $this->actividad();
        $this->persona(10011, 'Pla, Joan');
        $this->asistente(10011, true, PlazaId::ASIGNADA, 'xxx');

        $this->assertStringContainsString('Pla, Joan', $this->widget()->getSegmentData()['msg_err']);
    }

    public function test_el_resumen_de_plazas_de_una_actividad_publicada(): void
    {
        $this->apps(['asistentes', 'actividadplazas']);
        $a = new ActividadAll();
        $a->setId_activ(self::ID_ACTIV);
        $a->setId_tipo_activ(self::TIPO_CA_N);
        $a->setDl_org('dlb');
        $a->setPlazas(30);
        $a->setPublicado(true);
        $this->actividades->method('findById')->willReturn($a);
        $resumen = $this->createMock(ResumenPlazasService::class);
        $resumen->method('getResumen')->willReturn([
            'dlb' => ['total_disponibles' => 20, 'total_cedidas' => 5, 'cedidas' => ['dlp' => 5], 'ocupadas' => ['dlb' => 12, 'dlp' => 3], 'conseguidas' => []],
        ]);
        $this->resumen = $resumen;

        $d = $this->widget()->getSegmentData();

        $this->assertStringStartsWith('dlb: 12(dlb) + 3(dlp)', $d['resumen_plazas']);
        $this->assertStringContainsString('ocupadas=15/(20) libres=5', $d['resumen_plazas']);
        // dlp tiene 5 cedidas y ocupa 3: dos filas vacías para las que le quedan.
        $this->assertSame(['dlp----4', 'dlp----5'], array_values(array_map(static fn (array $v) => $v[2], array_filter($d['tabla']['valores'], static fn ($v): bool => is_array($v) && str_starts_with((string) $v[2], 'dlp----')))));
    }

    public function test_sin_publicar_no_hay_resumen_de_plazas(): void
    {
        $this->apps(['asistentes', 'actividadplazas']);
        $this->actividad();
        $resumen = $this->createMock(ResumenPlazasService::class);
        $resumen->method('getResumen')->willReturn(['dlb' => ['total_disponibles' => 20, 'ocupadas' => ['dlb' => 1]]]);
        $this->resumen = $resumen;

        $d = $this->widget()->getSegmentData();

        $this->assertSame('', $d['resumen_plazas']);
        $this->assertSame('', $d['resumen_plazas2']);
    }

    public function test_una_actividad_que_no_existe_avisa(): void
    {
        $this->actividades->method('findById')->willReturn(null);

        $w = $this->widget();

        $this->assertSame([3101, 'a', 'Actividad', self::ID_ACTIV], [$w->getId_dossier(), $w->getPau(), $w->getObj_pau(), $w->getId_pau()]);
        $this->assertSame([], $w->getValores());
    }
}
