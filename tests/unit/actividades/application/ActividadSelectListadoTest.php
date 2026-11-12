<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\application\ActividadSelectListado;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\Importada;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;
use src\personas\domain\entity\PersonaSacd;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\PermAccion;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\value_objects\PauType;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Búsqueda de actividades: filtros de la consulta, botones y columnas según rol,
 * apps y oficina, filas con datos, «ocupado» y sin permiso, fases, importar,
 * nombre oculto de la otra sección, orden, y demasiados resultados.
 */
final class ActividadSelectListadoTest extends myTest
{
    private RoleRepositoryInterface&MockObject $roles;
    private ActividadPubRepositoryInterface&MockObject $pub;
    private ActividadRepositoryInterface&MockObject $actividades;
    private CasaRepositoryInterface&MockObject $casas;
    private CentroRepositoryInterface&MockObject $centros;
    private PreferenciaRepositoryInterface&MockObject $prefs;
    private TipoTarifaRepositoryInterface&MockObject $tarifas;
    private ImportadaRepositoryInterface&MockObject $importadas;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private ActividadProcesoTareaRepositoryInterface&MockObject $procesos;
    private ActividadCargoRepositoryInterface&MockObject $cargos;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<list<string>> */
    private array $consultas = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oPerm', 'oPermActividades', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $this->rol(1);
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $this->oficinas([]);
        $this->roles = $this->createMock(RoleRepositoryInterface::class);
        $this->roles->method('getArrayRolesPau')->willReturn([5 => PauType::PAU_CTR, 6 => PauType::PAU_CDC]);
        $this->pub = $this->createMock(ActividadPubRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->casas = $this->createMock(CasaRepositoryInterface::class);
        $this->casas->method('getArrayCasas')->willReturn([200 => 'Casa B', 201 => 'Casa A']);
        $this->centros = $this->createMock(CentroRepositoryInterface::class);
        $this->centros->method('getArrayCentrosCdc')->willReturn([]);
        $this->prefs = $this->createMock(PreferenciaRepositoryInterface::class);
        $this->tarifas = $this->createMock(TipoTarifaRepositoryInterface::class);
        $this->importadas = $this->createMock(ImportadaRepositoryInterface::class);
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
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

    private function rol(int $idRole, int $sfsv = 1): void
    {
        $u = new Usuario();
        $u->setId_usuario(7);
        $u->setId_role($idRole);
        $_SESSION['session_auth'] = array_merge($this->anterior['session_auth'] ?? [], [
            'sfsv' => $sfsv, 'esquema' => 'H-dlbv', 'idioma' => 'es_ES.UTF-8', 'id_usuario' => 7, 'MiUsuario' => $u,
        ]);
    }

    /** @param list<string> $nombres */
    private function apps(array $nombres): void
    {
        $ids = ['procesos' => 3, 'actividadcargos' => 4, 'asistentes' => 5, 'actividadplazas' => 6, 'actividadessacd' => 7, 'actividadescentro' => 8, 'asignaturas' => 9, 'ubiscamas' => 10];
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

    /** @param list<ActividadAll> $lista */
    private function devolver(array $lista, bool $pub = false): void
    {
        ($pub ? $this->pub : $this->actividades)->method('matching')->willReturnCallback(function ($c) use ($lista): array {
            $this->consultas[] = DescribeCriteria::filtros($c);
            return $lista;
        });
    }

    private function actividad(int $id, int $tipo, string $ini, ?int $ubi = 200, string $nom = ''): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setId_tipo_activ($tipo);
        $a->setNom_activ($nom !== '' ? $nom : "act $id");
        $a->setDl_org('dlb');
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($ini));
        $a->setId_ubi($ubi);
        return $a;
    }

    private function uc(): ActividadSelectListado
    {
        return new ActividadSelectListado(
            $this->roles, $this->pub, $this->actividades, $this->casas, $this->centros, $this->prefs,
            $this->tarifas, $this->importadas, $this->encargados, $this->procesos, $this->cargos,
        );
    }

    public function test_filtros_por_defecto_y_con_todos_los_campos(): void
    {
        $this->devolver([]);
        $this->uc()->ejecutar([], 0);
        $f = $this->consultas[0];
        $this->assertSame(['status eq 2', 'id_tipo_activ matches ^1.....'], array_slice($f, 0, 2));
        $this->assertStringStartsWith('f_ini between [', $f[2]);
        $this->assertCount(3, $f);

        $this->uc()->ejecutar([
            'status' => '9', 'id_tipo_activ' => '112021', 'id_ubi' => '200', 'nom_activ' => 'ca (n)',
            'dl_org' => 'dlp', 'publicado' => '2', 'periodo' => 'otro', 'empiezamin' => '1/1/2027', 'empiezamax' => '31/1/2027',
        ], 0);
        $this->assertSame([
            'id_tipo_activ matches ^112021', 'id_ubi eq 200', 'nom_activ matches_insensitive ca \\(n\\)',
            'f_ini between [2027-01-01,2027-01-31]', 'dl_org eq dlp', 'publicado eq false',
        ], $this->consultas[1]);
    }

    public function test_tipo_desde_los_desplegables_y_modo_publicar(): void
    {
        $this->devolver([]);
        $out = $this->uc()->ejecutar(['ssfsv' => 'sf', 'sasistentes' => 'agd', 'sactividad2' => 'ca-est', 'modo' => 'publicar', 'publicado' => '1'], 0);
        $this->assertTrue($out['extendida']);
        $this->assertSame('2322..', $out['id_tipo_activ_efectivo']);
        $this->assertContains('id_tipo_activ matches ^2322..', $this->consultas[0]);
        $this->assertContains('publicado eq false', $this->consultas[0], 'publicar busca las no publicadas aunque se pida publicado=1');
        $this->assertSame(['datos', 'publicar'], array_column($out['a_botones'], 'txt'));
    }

    public function test_botones_y_columnas_segun_oficina_y_apps(): void
    {
        $this->devolver([]);
        $this->apps(['actividadcargos', 'asistentes', 'actividadplazas', 'procesos', 'asignaturas', 'ubiscamas']);
        $this->oficinas(['des', 'est']);
        $out = $this->uc()->ejecutar([], 0);
        $this->assertSame([
            'datos', 'duplicar', 'borrar', 'cambiar tipo', 'cargos', 'lista cl', 'asistentes', 'otras peticiones', 'lista',
            'plazas', 'proceso', 'asignaturas', 'plan estudios', 'listas de clase', 'posibles asignaturas', 'habitaciones',
        ], array_column($out['a_botones'], 'txt'));
        $this->assertSame(['inicio', 'fin', 'Actividad', 'hora ini', 'hora fin', 'sf/sv', 'tar.', 'Sacd', 'dl org'], array_column($out['a_cabeceras'], 'name'));
        $this->assertTrue($out['perm_nueva']);

        $this->oficinas([]);
        $this->apps([]);
        $this->assertSame(['datos'], array_column($this->uc()->ejecutar([], 0)['a_botones'], 'txt'));
    }

    public function test_rol_casa_solo_ve_datos_y_no_crea(): void
    {
        $this->devolver([]);
        $this->rol(6);
        $this->apps(['asistentes']);
        $out = $this->uc()->ejecutar([], 0);
        $this->assertSame(['datos'], array_column($out['a_botones'], 'txt'));
        $this->assertFalse($out['perm_nueva']);
    }

    public function test_rol_centro_no_ve_sacd_ni_dl_org(): void
    {
        $this->rol(5);
        $this->apps(['actividadescentro']);
        $a = $this->actividad(10, 112021, '2027-03-01');
        $a->setObserv('obs');
        $this->devolver([$a]);
        $c = new CentroDl();
        $c->setNombre_ubi('Ctr A');
        $this->encargados->method('getCentrosEncargadosActividad')->willReturn([$c]);
        $out = $this->uc()->ejecutar([], 0);
        $this->assertNotContains('Sacd', array_column($out['a_cabeceras'], 'name'));
        $fila = $out['a_valores'][0];
        $this->assertSame(['Ctr A', 'obs'], [$fila[8], $fila[9]]);
        $this->assertArrayNotHasKey(11, $fila);
        $this->assertFalse($out['perm_nueva']);
    }

    public function test_fila_con_todos_los_datos(): void
    {
        $this->apps(['actividadessacd', 'actividadescentro']);
        $this->oficinas(['vcsd']);
        $a = $this->actividad(10, 112021, '2027-03-01', nom: 'ca n');
        $a->setH_ini(TimeLocal::fromString('20:00:00'));
        $a->setTarifa(4);
        $a->setIdiomaVo('ca_ES');
        $this->devolver([$a]);
        $t = new TipoTarifa();
        $t->setLetraVo('B');
        $this->tarifas->method('findById')->with(4)->willReturn($t);
        $s1 = new PersonaSacd();
        $s1->setNom('Juan');
        $s1->setApellido1('García');
        $this->cargos->method('getActividadSacds')->with(10)->willReturn([$s1]);
        $this->encargados->method('getCentrosEncargadosActividad')->willReturn([]);

        $out = $this->uc()->ejecutar(['sel' => ['10#ca n'], 'scroll_id' => '5'], 0);

        $fila = $out['a_valores'][0];
        $this->assertSame('10#ca n', $fila['sel']);
        $this->assertSame(['id_activ' => 10], HashB::open($fila['ctx_eliminar'], 'actividad_eliminar'));
        $this->assertSame(['script' => 'jsForm.mandar("#seleccionados","dossiers")', 'valor' => 'ca n'], $fila[3]);
        $this->assertSame(['20:00', '', 'sv', 'B', 'dlb', '', null, 'ca_ES'], [$fila[4], $fila[5], $fila[6], $fila[7], $fila[9], $fila[10], $fila[11], $fila[12]]);
        $this->assertStringContainsString('Juan', $fila[8]);
        $this->assertSame(['10#ca n'], $out['a_valores']['select']);
        $this->assertSame('5', $out['a_valores']['scroll_id']);
        $this->assertStringStartsWith('1 actividades encontradas entre ', $out['resultado']);
    }

    public function test_preferencia_html_enlaza_al_dossier(): void
    {
        $this->devolver([$this->actividad(10, 112021, '2027-03-01')]);
        $pref = new \src\usuarios\domain\entity\Preferencia();
        $pref->setPreferenciaVo('html');
        $this->prefs->method('findById')->willReturn($pref);
        $celda = $this->uc()->ejecutar([], 0)['a_valores'][0][3];
        $this->assertSame(['pau' => 'a', 'id_pau' => 10, 'obj_pau' => 'Actividad'], $celda['link_spec']['query']);
    }

    public function test_orden_por_fecha_y_casa(): void
    {
        $this->devolver([
            $this->actividad(1, 112021, '2027-03-02', 201),
            $this->actividad(2, 112021, '2027-03-01', 200),
            $this->actividad(3, 112021, '2027-03-01', 201),
            $this->actividad(4, 112021, '2027-03-01', null),
        ]);
        $sel = array_column(array_filter($this->uc()->ejecutar([], 0)['a_valores'], 'is_int', ARRAY_FILTER_USE_KEY), 'sel');
        // Mismo día: Casa A (201) antes que Casa B (200); sin casa ('z') al final.
        $this->assertSame(['3#act 3', '2#act 2', '4#act 4', '1#act 1'], $sel);
    }

    public function test_actividad_de_la_otra_seccion_muestra_solo_el_tipo_salvo_a_des(): void
    {
        $this->devolver([$this->actividad(10, 212021, '2027-03-01', nom: 'nombre sf')]);
        $this->assertSame('10#sf ca', $this->uc()->ejecutar([], 0)['a_valores'][0]['sel']);
        $this->oficinas(['des']);
        $this->assertSame('10#nombre sf', $this->uc()->ejecutar([], 0)['a_valores'][0]['sel']);
    }

    private function permisos(array $porActividad): void
    {
        $actual = 0;
        $perm = $this->createMock(PermisosActividades::class);
        $perm->method('setActividad')->willReturnCallback(function (int $id) use (&$actual): void {
            $actual = $id;
        });
        $perm->method('getPermisoActual')->willReturnCallback(function () use (&$actual, $porActividad): PermAccion {
            $a = $this->createMock(PermAccion::class);
            $a->method('have_perm_activ')->willReturnCallback(fn (string $p): bool => in_array($p, $porActividad[$actual] ?? [], true));
            return $a;
        });
        $_SESSION['oPermActividades'] = $perm;
    }

    public function test_con_procesos_ocupado_y_sin_permiso(): void
    {
        $this->apps(['procesos']);
        $this->permisos([10 => ['ocupado', 'ver'], 11 => ['ocupado'], 12 => []]);
        $this->devolver([
            $this->actividad(10, 112021, '2027-03-01'),
            $this->actividad(11, 112021, '2027-03-02'),
            $this->actividad(12, 112021, '2027-03-03'),
        ]);
        $out = $this->uc()->ejecutar([], 0);
        $filas = array_values(array_filter($out['a_valores'], 'is_int', ARRAY_FILTER_USE_KEY));
        $this->assertCount(2, $filas);
        $this->assertSame('10#act 10', $filas[0]['sel']);
        $this->assertSame('', $filas[1]['sel']);
        $this->assertStringStartsWith('ocupado sv (', $filas[1][3]);
        $this->assertStringStartsWith('3 actividades encontradas (1 sin permiso)', $out['resultado']);
    }

    public function test_filtro_de_fases_completadas_y_no_completadas(): void
    {
        $this->apps(['procesos']);
        $this->devolver([$this->actividad(10, 112021, '2027-03-01'), $this->actividad(11, 112021, '2027-03-02')]);
        $this->procesos->method('getFasesCompletadas')->willReturnCallback(fn (int $id): array => $id === 10 ? [1, 2] : [1]);
        $sel = fn (array $in): array => array_column(array_filter($this->uc()->ejecutar($in, 0)['a_valores'], 'is_int', ARRAY_FILTER_USE_KEY), 'sel');
        $this->assertSame(['10#act 10'], $sel(['fases_on' => ['2']]));
        $this->assertSame(['11#act 11'], $sel(['fases_off' => ['2']]));
    }

    public function test_importar_busca_en_publicadas_de_otras_dl_y_salta_las_importadas(): void
    {
        $this->devolver([$this->actividad(10, 112021, '2027-03-01'), $this->actividad(11, 112021, '2027-03-02')], true);
        $this->actividades->expects($this->never())->method('matching');
        $this->importadas->method('findById')->willReturnCallback(fn (int $id): ?Importada => $id === 10 ? new Importada() : null);
        $out = $this->uc()->ejecutar(['modo' => 'importar'], 0);
        $this->assertSame(['importar', 'ActividadPub'], [$out['mod'], $out['obj_pau']]);
        $this->assertContains('dl_org neq dlb', $this->consultas[0]);
        $filas = array_values(array_filter($out['a_valores'], 'is_int', ARRAY_FILTER_USE_KEY));
        $this->assertCount(1, $filas);
        $this->assertSame('act 11', $filas[0][3]);
        $this->assertSame(['id_activ' => 11], HashB::open($filas[0]['ctx_importar'], 'actividad_importar'));
        $this->assertSame(['importar'], array_column($out['a_botones'], 'txt'));
    }

    public function test_demasiadas_actividades_pide_confirmar(): void
    {
        $this->devolver(array_map(fn (int $i) => $this->actividad($i, 112021, '2027-03-01'), range(1, 201)));
        $out = $this->uc()->ejecutar([], 4);
        $this->assertSame([], $out['a_valores']);
        $this->assertSame(201, $out['advertencia_demasiadas']['num_actividades']);
        $this->assertSame(['continuar' => 'si', 'Gstack' => 4], $out['advertencia_demasiadas']['continuar_link_spec']['query']);
        $this->assertCount(201, array_filter($this->uc()->ejecutar(['continuar' => 'si'], 4)['a_valores'], 'is_int', ARRAY_FILTER_USE_KEY));
    }

    public function test_tipos_para_crear(): void
    {
        $this->devolver([]);
        $out = $this->uc()->ejecutar(['id_tipo_activ' => '112021'], 0);
        $this->assertSame('112021', $out['aTiposActiv']['del mismo tipo']);
        $this->assertGreaterThan(1, count($out['aTiposActiv']));
        $todas = $this->uc()->ejecutar(['ssfsv' => 'sv'], 0);
        $this->assertSame(['del mismo tipo' => '1'], $todas['aTiposActiv']);
    }
}
