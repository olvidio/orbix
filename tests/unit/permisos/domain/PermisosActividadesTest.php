<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain;

use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XResto;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\TareaProceso;
use src\shared\config\ConfigGlobal;
use Tests\myTest;
use Tests\unit\permisos\domain\support\PermisosActividadesConMatriz as P;

/**
 * Matriz de permisos por tipo de actividad (sin BD: se siembra a mano).
 *
 * Convención de las reglas: [afecta, fase_ref, perm_on, perm_off]; los permisos son los de
 * PermAccion (ocupado=1, ver=3, modificar=7, crear=15, borrar=31). `on` rige cuando la fase
 * de referencia está completada y `off` cuando no.
 */
final class PermisosActividadesTest extends myTest
{
    private const D = PermisosActividades::AFECTA['datos'];
    private const SACD = PermisosActividades::AFECTA['sacd'];
    private const ASIS_SACD = PermisosActividades::AFECTA['asistentesSacd'];
    private const TIPO = '171001';

    /** @var array<string, mixed> */
    private array $configAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = [
            'a_apps' => $_SESSION['config']['a_apps'] ?? [],
            'app_installed' => $_SESSION['config']['app_installed'] ?? [],
        ];
    }

    public function tearDown(): void
    {
        $_SESSION['config']['a_apps'] = $this->configAnterior['a_apps'];
        $_SESSION['config']['app_installed'] = $this->configAnterior['app_installed'];
        parent::tearDown();
    }

    /** Actividad propia (dl_org = mi delegación), con las fases indicadas ya completadas. */
    private function actividadPropia(P $p, array $fasesCompletadas, string $tipo = self::TIPO): P
    {
        $p->setActividad(42, $tipo, ConfigGlobal::mi_delef());
        $p->setFasesCompletadas($fasesCompletadas);

        return $p;
    }

    private function perm(P $p, string $afecta, string $accion): bool
    {
        return $p->getPermisoActual($afecta)->have_perm_activ($accion);
    }

    // ---- constantes y accesores -----------------------------------------------

    public function test_los_afecta_son_bits_distintos(): void
    {
        $this->assertSame(
            ['datos' => 1, 'economic' => 2, 'sacd' => 4, 'ctr' => 8, 'id_tarifa' => 16, 'cargos' => 32, 'asistentes' => 64, 'asistentesSacd' => 128],
            PermisosActividades::AFECTA
        );
        $this->assertCount(8, array_unique(PermisosActividades::AFECTA));
        $this->assertSame(PermisosActividades::AFECTA, (new P())->getAfecta());
    }

    public function test_set_y_get_id_fase_y_tipo_proceso(): void
    {
        $p = new P();
        $this->assertSame(0, $p->getId_fase());
        $this->assertSame(0, $p->getId_tipo_proceso());

        $p->setId_fase(12);
        $p->setId_tipo_proceso(34);

        $this->assertSame(12, $p->getId_fase());
        $this->assertSame(34, $p->getId_tipo_proceso());
    }

    /**
     * @dataProvider valoresPropiaProvider
     */
    public function test_set_propia(bool|string $valor, bool $esperado): void
    {
        $p = new P(!$esperado);

        $p->setPropia($valor);

        $this->assertSame($esperado, $p->estado()['bpropia']);
    }

    /**
     * @return iterable<string, array{bool|string, bool}>
     */
    public static function valoresPropiaProvider(): iterable
    {
        yield 'true' => [true, true];
        yield 't de postgres' => ['t', true];
        yield 'false' => [false, false];
        yield 'f de postgres' => ['f', false];
        yield 'cadena vacía' => ['', false];
    }

    public function test_set_id_tipo_activ_normaliza_y_marca_el_tope(): void
    {
        $p = new P();

        $p->setId_tipo_activ('17');
        $this->assertSame('17....', $p->estado()['sid_tipo_activ']);
        $this->assertFalse($p->estado()['btop']);

        $p->setId_tipo_activ('......');
        $this->assertTrue($p->estado()['btop']);

        $p->setId_tipo_activ('000017');
        $this->assertSame('17....', $p->estado()['sid_tipo_activ']);
        $this->assertFalse($p->estado()['btop'], 'cambiar a otro tipo quita la marca de tope');

        $p->setId_tipo_activ('171001');
        $this->assertSame('171001', $p->estado()['sid_tipo_activ']);
    }

    public function test_set_id_activ_invalida_la_cache_solo_si_cambia_el_id(): void
    {
        $p = new P();
        $p->setActividad(42, self::TIPO, 'dlx');
        $p->setFasesCompletadas([10, 11]);

        $p->setId_activ(42);
        $this->assertSame([10, 11], $p->estado()['aFasesCompletadas'], 'mismo id: se conserva');
        $this->assertSame(self::TIPO, $p->estado()['setActividadContextTipo']);

        $p->setId_activ(43);
        $this->assertSame([], $p->estado()['aFasesCompletadas']);
        $this->assertNull($p->estado()['setActividadContextTipo']);
        $this->assertNull($p->estado()['setActividadContextDlOrg']);
        $this->assertSame(43, $p->estado()['iid_activ']);
    }

    // ---- setActividad --------------------------------------------------------------

    /**
     * @dataProvider delegacionesProvider
     */
    public function test_set_actividad_con_contexto_decide_si_es_propia(string $dlOrg, bool $propia): void
    {
        // Los data providers se evalúan antes de que exista la sesión: los marcadores se resuelven aquí.
        $dlOrg = str_replace(['{mi_delef}', '{mi_dele}'], [ConfigGlobal::mi_delef(), ConfigGlobal::mi_dele()], $dlOrg);
        $p = new P(!$propia);

        $p->setActividad(1, self::TIPO, $dlOrg);

        $this->assertSame($propia, $p->estado()['bpropia'], $dlOrg);
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function delegacionesProvider(): iterable
    {
        yield 'mi delegación' => ['{mi_delef}', true];
        yield 'mi delegación con f (actividad sf)' => ['{mi_dele}f', true];
        yield 'otra delegación' => ['dl_otra', false];
        yield 'otra delegación terminada en f' => ['dl_otraf', false];
    }

    public function test_set_actividad_con_contexto_normaliza_el_tipo_y_registra_el_contexto(): void
    {
        $p = new P();

        $p->setActividad(7, '17', 'dlx');

        $e = $p->estado();
        $this->assertSame('17....', $e['sid_tipo_activ']);
        $this->assertSame(7, $e['iid_activ']);
        $this->assertSame('17', $e['setActividadContextTipo']);
        $this->assertSame('dlx', $e['setActividadContextDlOrg']);
    }

    public function test_set_actividad_con_otro_id_reinicia_fases_y_tope(): void
    {
        $p = new P();
        $p->setActividad(1, self::TIPO, 'dlx');
        $p->setFasesCompletadas([10]);
        $p->setId_tipo_activ('......');
        $this->assertTrue($p->estado()['btop']);

        $p->setActividad(1, self::TIPO, 'dlx');
        $this->assertSame([10], $p->estado()['aFasesCompletadas'], 'mismo id: conserva las fases');
        $this->assertFalse($p->estado()['btop'], 'setActividad siempre quita el tope');

        $p->setActividad(2, self::TIPO, 'dlx');
        $this->assertSame([], $p->estado()['aFasesCompletadas']);
    }

    public function test_set_actividad_sin_contexto_lo_busca_en_el_repositorio(): void
    {
        $oActividad = new ActividadAll();
        $oActividad->setId_tipo_activ((int) self::TIPO);
        $oActividad->setDl_org(ConfigGlobal::mi_delef());
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->expects($this->once())->method('findById')->with(42)->willReturn($oActividad);
        $p = (new P(false))->conRepositorios($repo);

        $p->setActividad(42);

        $e = $p->estado();
        $this->assertSame(self::TIPO, $e['sid_tipo_activ']);
        $this->assertTrue($e['bpropia']);
        $this->assertSame(self::TIPO, $e['setActividadContextTipo']);
    }

    public function test_set_actividad_reutiliza_el_contexto_sin_volver_al_repositorio(): void
    {
        $oActividad = new ActividadAll();
        $oActividad->setId_tipo_activ((int) self::TIPO);
        $oActividad->setDl_org('dl_otra');
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->expects($this->once())->method('findById')->willReturn($oActividad);
        $p = (new P())->conRepositorios($repo);

        $p->setActividad(42);
        $p->setId_tipo_activ('17....');
        $p->setActividad(42);

        $this->assertSame(self::TIPO, $p->estado()['sid_tipo_activ'], 'se restaura el tipo de la actividad');
        $this->assertFalse($p->estado()['bpropia']);
    }

    public function test_set_actividad_tras_cambiar_el_id_vuelve_a_consultar(): void
    {
        $oActividad = new ActividadAll();
        $oActividad->setId_tipo_activ((int) self::TIPO);
        $oActividad->setDl_org('dl_otra');
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->expects($this->exactly(2))->method('findById')->willReturn($oActividad);
        $p = (new P())->conRepositorios($repo);

        $p->setActividad(42);
        $p->setId_activ(43);
        $p->setActividad(43);
    }

    public function test_set_actividad_inexistente_lanza_excepcion(): void
    {
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $p = (new P())->conRepositorios($repo);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('actividad 99 no encontrada');
        $p->setActividad(99);
    }

    // ---- permiso actual (getPermisoActual) --------------------------------------------

    public function test_fase_completada_aplica_el_permiso_on_y_si_no_el_off(): void
    {
        $matriz = ['17....' => P::resto('17....', [[self::D, 10, 7, 3]])];
        $completada = $this->actividadPropia((new P())->conMatriz($matriz), [10]);
        $pendiente = $this->actividadPropia((new P())->conMatriz($matriz), [99]);

        $this->assertTrue($this->perm($completada, 'datos', 'modificar'));
        $this->assertFalse($this->perm($completada, 'datos', 'crear'));
        $this->assertTrue($this->perm($pendiente, 'datos', 'ver'));
        $this->assertFalse($this->perm($pendiente, 'datos', 'modificar'));
    }

    public function test_actividad_de_otra_dl_usa_la_matriz_de_otras(): void
    {
        $dl = ['17....' => P::resto('17....', [[self::D, 10, 15, 15]])];
        $otras = ['17....' => P::resto('17....', [[self::D, 10, 3, 1]])];
        $p = (new P())->conMatriz($dl, $otras);
        $p->setActividad(42, self::TIPO, 'dl_otra');
        $p->setFasesCompletadas([10]);

        $this->assertTrue($this->perm($p, 'datos', 'ver'));
        $this->assertFalse($this->perm($p, 'datos', 'modificar'), 'la regla de la dl propia no se aplica');
    }

    public function test_la_regla_mas_especifica_gana(): void
    {
        $p = (new P())->conMatriz([
            '17....' => P::resto('17....', [[self::D, 10, 3, 3]]),
            '171...' => P::resto('171...', [[self::D, 20, 31, 31]]),
        ]);
        $this->actividadPropia($p, [20]);

        $this->assertTrue($this->perm($p, 'datos', 'borrar'), 'manda 171..., no 17....');
    }

    public function test_si_el_afecta_no_esta_en_el_nivel_especifico_sube_al_padre(): void
    {
        $p = (new P())->conMatriz([
            '17....' => P::resto('17....', [[self::D, 10, 3, 3], [self::SACD, 10, 15, 15]]),
            '171...' => P::resto('171...', [[self::D, 20, 31, 31]]),
        ]);
        $this->actividadPropia($p, [10, 20]);

        $this->assertTrue($this->perm($p, 'sacd', 'crear'), 'sacd solo está definido en 17....');
        $this->assertTrue($this->perm($p, 'datos', 'borrar'), 'y datos sigue resolviéndose en 171...');
    }

    public function test_repetir_consultas_con_distinto_afecta_no_arrastra_el_nivel_del_arbol(): void
    {
        $p = (new P())->conMatriz([
            '17....' => P::resto('17....', [[self::SACD, 10, 15, 15]]),
            '171...' => P::resto('171...', [[self::D, 20, 31, 31]]),
        ]);
        $this->actividadPropia($p, [10, 20]);

        // datos baja hasta 171...; sacd debe volver a empezar desde el tipo de la actividad.
        $this->assertTrue($this->perm($p, 'datos', 'borrar'));
        $this->assertTrue($this->perm($p, 'sacd', 'crear'));
        $this->assertTrue($this->perm($p, 'datos', 'borrar'));
    }

    public function test_una_regla_en_la_raiz_se_encuentra_pero_get_permiso_actual_no_la_aplica(): void
    {
        // Comportamiento actual: al subir hasta '......' se marca el tope (btop) y getPermisoActual
        // devuelve «sin permiso» antes de usar esa regla. En los datos reales no hay reglas '......'.
        $r = P::resto('......', [[self::D, 5, 7, 7]]);
        $p = (new P())->conMatriz(['......' => $r]);
        $this->actividadPropia($p, [5]);

        $this->assertFalse($this->perm($p, 'datos', 'ver'));
        $this->assertSame($r, $p->getPermisos(self::D, self::TIPO), 'getPermisos sí la localiza');
    }

    public function test_una_regla_de_primer_nivel_vale_para_sus_subtipos(): void
    {
        $p = (new P())->conMatriz(['1.....' => P::resto('1.....', [[self::D, 5, 7, 7]])]);
        $this->actividadPropia($p, [5]);

        $this->assertTrue($this->perm($p, 'datos', 'modificar'));
    }

    public function test_sin_ninguna_regla_aplicable_no_hay_permiso(): void
    {
        $p = (new P())->conMatriz(['2.....' => P::resto('2.....', [[self::D, 5, 31, 31]])]);
        $this->actividadPropia($p, [5]);

        $this->assertFalse($this->perm($p, 'datos', 'ver'), 'la regla es de otro tipo');
        $this->assertFalse($this->perm($p, 'datos', 'ocupado'));
    }

    public function test_matriz_vacia_no_da_permiso(): void
    {
        $p = $this->actividadPropia(new P(), [5]);

        $this->assertFalse($this->perm($p, 'datos', 'ver'));
    }

    public function test_el_tope_del_arbol_devuelve_sin_permiso(): void
    {
        $p = (new P())->conMatriz(['......' => P::resto('......', [[self::D, 5, 31, 31]])]);
        $p->setId_tipo_activ('......');
        $p->setFasesCompletadas([5]);

        $this->assertFalse($this->perm($p, 'datos', 'ocupado'), 'btop: no se resuelve nada');
    }

    public function test_la_actividad_puede_tener_regla_exacta_de_6_cifras(): void
    {
        $p = (new P())->conMatriz([
            '17....' => P::resto('17....', [[self::D, 10, 3, 3]]),
            self::TIPO => P::resto(self::TIPO, [[self::D, 10, 15, 15]]),
        ]);
        $this->actividadPropia($p, [10]);

        $this->assertTrue($this->perm($p, 'datos', 'crear'));
    }

    public function test_sin_fases_completadas_ni_id_de_actividad_lanza_excepcion(): void
    {
        $p = (new P())->conMatriz(['17....' => P::resto('17....', [[self::D, 10, 7, 3]])]);
        $p->setId_tipo_activ(self::TIPO);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('sin fases en caché');
        $p->getPermisoActual('datos');
    }

    public function test_si_no_hay_fases_en_cache_se_cargan_una_vez_del_repositorio(): void
    {
        $repoFases = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $repoFases->expects($this->once())->method('getFasesCompletadas')->with(42)->willReturn([10]);
        $p = (new P())
            ->conMatriz(['17....' => P::resto('17....', [[self::D, 10, 7, 3], [self::SACD, 10, 15, 0]])])
            ->conRepositorios(actividad: $this->createMock(ActividadAllRepositoryInterface::class), procesoTarea: $repoFases);
        $p->setActividad(42, self::TIPO, ConfigGlobal::mi_delef());

        $this->assertTrue($this->perm($p, 'datos', 'modificar'));
        $this->assertTrue($this->perm($p, 'sacd', 'crear'), 'la segunda consulta usa las fases ya cargadas');
    }

    // ---- permiso on (getPermisoOn) ---------------------------------------------------------

    public function test_permiso_on_solo_si_la_fase_esta_completada(): void
    {
        $matriz = ['17....' => P::resto('17....', [[self::SACD, 10, 15, 7]])];
        $completada = $this->actividadPropia((new P())->conMatriz($matriz), [10]);
        $pendiente = $this->actividadPropia((new P())->conMatriz($matriz), [99]);

        $this->assertTrue($completada->getPermisoOn('sacd')->have_perm_activ('crear'));
        $this->assertFalse($pendiente->getPermisoOn('sacd')->have_perm_activ('ocupado'), 'sin completar no hay permiso on');
    }

    public function test_permiso_on_acepta_el_afecta_como_texto_o_como_entero(): void
    {
        $p = $this->actividadPropia(
            (new P())->conMatriz(['17....' => P::resto('17....', [[self::SACD, 10, 7, 0]])]),
            [10]
        );

        $this->assertTrue($p->getPermisoOn('sacd')->have_perm_activ('modificar'));
        $this->assertTrue($p->getPermisoOn(self::SACD)->have_perm_activ('modificar'));
    }

    public function test_permiso_on_de_un_afecta_sin_regla_es_cero(): void
    {
        $p = $this->actividadPropia(
            (new P())->conMatriz(['17....' => P::resto('17....', [[self::SACD, 10, 7, 0]])]),
            [10]
        );

        $this->assertFalse($p->getPermisoOn('economic')->have_perm_activ('ocupado'));
    }

    // ---- getPermisos ---------------------------------------------------------------------------

    public function test_get_permisos_devuelve_la_regla_del_nivel_que_define_el_afecta(): void
    {
        $r17 = P::resto('17....', [[self::D, 10, 3, 3]]);
        $r171 = P::resto('171...', [[self::SACD, 11, 7, 7]]);
        $p = (new P())->conMatriz(['17....' => $r17, '171...' => $r171]);

        $this->assertSame($r171, $p->getPermisos(self::SACD, '171...'));
        $this->assertSame($r17, $p->getPermisos(self::D, '171...'), 'datos no está en 171...: sube a 17....');
        $this->assertSame($r17, $p->getPermisos(self::D, self::TIPO));
    }

    public function test_get_permisos_sin_regla_devuelve_false(): void
    {
        $p = (new P())->conMatriz(['2.....' => P::resto('2.....', [[self::D, 1, 3, 3]])]);

        $this->assertFalse($p->getPermisos(self::D, self::TIPO));
        $this->assertFalse((new P())->getPermisos(self::D, self::TIPO));
    }

    public function test_get_permisos_usa_el_tipo_actual_si_no_se_indica(): void
    {
        $r = P::resto('17....', [[self::D, 10, 3, 3]]);
        $p = (new P())->conMatriz(['17....' => $r]);
        $p->setId_tipo_activ(self::TIPO);

        $this->assertSame($r, $p->getPermisos(self::D));
    }

    public function test_get_permisos_reconoce_claves_con_formato_antiguo(): void
    {
        $r = P::resto('17', [[self::D, 10, 3, 3]]);
        $p = (new P())->conMatriz(['17' => $r]);

        $this->assertSame($r, $p->getPermisos(self::D, '17....'));
    }

    public function test_get_permisos_respeta_si_la_actividad_es_propia(): void
    {
        $rDl = P::resto('17....', [[self::D, 10, 15, 15]]);
        $rOtras = P::resto('17....', [[self::D, 10, 3, 3]]);

        $propia = (new P(true))->conMatriz(['17....' => $rDl], ['17....' => $rOtras]);
        $ajena = (new P(false))->conMatriz(['17....' => $rDl], ['17....' => $rOtras]);

        $this->assertSame($rDl, $propia->getPermisos(self::D, '17....'));
        $this->assertSame($rOtras, $ajena->getPermisos(self::D, '17....'));
    }

    // ---- sacd (havePermisoSacd / motivoSinPermisoSacd) ---------------------------------------------

    /**
     * @param list<array{0: int, 1: int|string, 2: int, 3: int}> $reglas
     */
    private function sacd(array $reglas, array $fasesCompletadas): P
    {
        return $this->actividadPropia((new P())->conMatriz(['17....' => P::resto('17....', $reglas)]), $fasesCompletadas);
    }

    /** datos visibles siempre (fase 2), sacd en la fase 10 y asistentes sacd en la 11. */
    private function reglasSacd(): array
    {
        return [
            [self::D, 2, 3, 3],
            [self::SACD, 10, 3, 0],
            [self::ASIS_SACD, 11, 3, 0],
        ];
    }

    public function test_sacd_con_cargo_ve_la_actividad_si_su_fase_esta_completada(): void
    {
        $p = $this->sacd($this->reglasSacd(), [2, 10]);

        $this->assertTrue($p->havePermisoSacd(7, false));
        $this->assertNull($p->motivoSinPermisoSacd(7, false));
    }

    public function test_sacd_con_cargo_sin_la_fase_sacd_completada_no_ve_la_actividad(): void
    {
        $p = $this->sacd($this->reglasSacd(), [2]);

        $this->assertFalse($p->havePermisoSacd(7, false));
        $motivo = (string) $p->motivoSinPermisoSacd(7, false);
        $this->assertStringContainsString('fase sacd', $motivo);
        $this->assertStringNotContainsString('asistentes sacd', $motivo, 'si no asiste, no se menciona esa fase');
    }

    public function test_sacd_con_cargo_que_tambien_asiste_puede_entrar_por_la_fase_de_asistentes(): void
    {
        $p = $this->sacd($this->reglasSacd(), [2, 11]);

        $this->assertTrue($p->havePermisoSacd(7, true));
        $this->assertFalse($p->havePermisoSacd(7, false), 'sin asistir, la fase de asistentes no cuenta');
    }

    public function test_sacd_con_cargo_y_asistencia_menciona_las_dos_fases_si_ninguna_vale(): void
    {
        $p = $this->sacd($this->reglasSacd(), [2]);

        $motivo = (string) $p->motivoSinPermisoSacd(7, true);

        $this->assertStringContainsString('fase sacd', $motivo);
        $this->assertStringContainsString('fase asistentes sacd', $motivo);
    }

    public function test_sacd_sin_cargo_solo_depende_de_la_fase_de_asistentes(): void
    {
        $this->assertTrue($this->sacd($this->reglasSacd(), [2, 11])->havePermisoSacd(null, true));
        $this->assertFalse($this->sacd($this->reglasSacd(), [2, 10])->havePermisoSacd(null, true), 'la fase sacd no basta si no tiene cargo');
        $this->assertFalse($this->sacd($this->reglasSacd(), [2, 10])->havePermisoSacd(0, true), 'id_cargo 0 equivale a sin cargo');
    }

    public function test_sacd_sin_cargo_explica_que_solo_asiste(): void
    {
        $motivo = (string) $this->sacd($this->reglasSacd(), [2])->motivoSinPermisoSacd(null, true);

        $this->assertStringContainsString('fase asistentes sacd', $motivo);
        $this->assertStringNotContainsString('fase sacd,', $motivo);
    }

    public function test_sacd_sin_permiso_de_ver_los_datos_no_entra_aunque_las_fases_esten_completas(): void
    {
        $reglas = [[self::D, 2, 1, 1], [self::SACD, 10, 3, 0], [self::ASIS_SACD, 11, 3, 0]];
        $p = $this->sacd($reglas, [2, 10, 11]);

        $this->assertFalse($p->havePermisoSacd(7, true));
        $this->assertStringContainsString('datos', (string) $p->motivoSinPermisoSacd(7, true));
    }

    public function test_sacd_fase_completada_pero_sin_permiso_de_ver(): void
    {
        $reglas = [[self::D, 2, 3, 3], [self::SACD, 10, 1, 0]];
        $p = $this->sacd($reglas, [2, 10]);

        $this->assertFalse($p->havePermisoSacd(7, false));
        $this->assertStringContainsString('no hay permiso de ver', (string) $p->motivoSinPermisoSacd(7, false));
    }

    public function test_sacd_sin_fase_definida_para_el_tipo(): void
    {
        $reglas = [[self::D, 2, 3, 3]];
        $p = $this->sacd($reglas, [2]);

        $this->assertFalse($p->havePermisoSacd(7, false));
        $this->assertStringContainsString('no hay fase sacd definida', (string) $p->motivoSinPermisoSacd(7, false));
    }

    public function test_sacd_sin_regla_de_datos_no_entra(): void
    {
        $p = $this->sacd([[self::SACD, 10, 3, 0]], [10]);

        $this->assertFalse($p->havePermisoSacd(7, false));
    }

    // ---- crear (getPermisoCrear) -----------------------------------------------------------------------

    private function instalarProcesos(bool $instalada): void
    {
        $_SESSION['config']['a_apps']['procesos'] = 987;
        $_SESSION['config']['app_installed'] = $instalada ? [987] : [];
    }

    private function tarea(string $responsable, int $status): TareaProceso&MockObject
    {
        $t = $this->createMock(TareaProceso::class);
        $t->method('getOf_responsable_txt')->willReturn($responsable);
        $t->method('getStatus')->willReturn($status);

        return $t;
    }

    /**
     * @param list<int> $procesos
     * @param array<int, list<TareaProceso>|\Throwable> $tareasPorProceso
     */
    private function paraCrear(array $procesos, array $tareasPorProceso, array $reglas, bool $propia = true): P
    {
        $tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipos->method('getTiposDeProcesos')->willReturn($procesos);
        $tareas = $this->createMock(TareaProcesoRepositoryInterface::class);
        $tareas->method('getFaseIndependiente')->willReturnCallback(
            static function (int $idProceso) use ($tareasPorProceso) {
                $r = $tareasPorProceso[$idProceso];
                if ($r instanceof \Throwable) {
                    throw $r;
                }

                return $r;
            }
        );
        $matriz = ['17....' => P::resto('17....', $reglas)];
        $p = (new P($propia))
            ->conMatriz($propia ? $matriz : [], $propia ? [] : $matriz)
            ->conRepositorios(actividad: $this->createMock(ActividadAllRepositoryInterface::class), tipoDeActividad: $tipos, tareaProceso: $tareas);
        $p->setId_tipo_activ(self::TIPO);

        return $p;
    }

    public function test_crear_sin_la_app_procesos_siempre_se_puede(): void
    {
        $this->instalarProcesos(false);

        $this->assertSame(['of_responsable_txt' => '', 'status' => 0], (new P())->getPermisoCrear(true));
    }

    public function test_crear_pide_permiso_de_crear_en_datos_cuando_la_fase_aprobada_esta_off(): void
    {
        $this->instalarProcesos(true);
        $p = $this->paraCrear([5], [5 => [$this->tarea('sg', 2)]], [[self::D, 2, 0, 15]]);

        $this->assertSame(['of_responsable_txt' => 'sg', 'status' => 2], $p->getPermisoCrear(true));
    }

    public function test_crear_con_permiso_inferior_a_crear_devuelve_false(): void
    {
        $this->instalarProcesos(true);
        $p = $this->paraCrear([5], [5 => [$this->tarea('sg', 2)]], [[self::D, 2, 15, 7]]);

        $this->assertFalse($p->getPermisoCrear(true), 'solo cuenta el permiso off: modificar no basta');
    }

    public function test_crear_sin_regla_de_permisos_devuelve_false(): void
    {
        $this->instalarProcesos(true);
        $p = $this->paraCrear([5], [5 => [$this->tarea('sg', 2)]], []);

        $this->assertFalse($p->getPermisoCrear(true));
    }

    public function test_crear_toma_el_primer_proceso_que_da_permiso(): void
    {
        $this->instalarProcesos(true);
        $p = $this->paraCrear(
            [5, 6],
            [5 => new RuntimeException('sin fase independiente'), 6 => [$this->tarea('scdl', 1)]],
            [[self::D, 2, 0, 15]]
        );

        $this->assertSame(['of_responsable_txt' => 'scdl', 'status' => 1], $p->getPermisoCrear(true));
    }

    public function test_crear_si_ningun_proceso_tiene_fase_independiente_devuelve_false(): void
    {
        $this->instalarProcesos(true);
        $p = $this->paraCrear([5, 6], [5 => new RuntimeException('a'), 6 => new RuntimeException('b')], [[self::D, 2, 0, 15]]);

        $this->assertFalse($p->getPermisoCrear(true));
    }

    public function test_crear_actividad_de_otra_dl_usa_la_matriz_de_otras(): void
    {
        $this->instalarProcesos(true);
        $p = $this->paraCrear([5], [5 => [$this->tarea('sg', 2)]], [[self::D, 2, 0, 15]], false);

        $this->assertSame(['of_responsable_txt' => 'sg', 'status' => 2], $p->getPermisoCrear(false));
        $this->assertFalse($p->getPermisoCrear(true), 'con dl propia no hay regla en su matriz');
    }

    public function test_crear_sin_procesos_para_el_tipo_avisa_y_devuelve_false(): void
    {
        $this->instalarProcesos(true);
        $p = $this->paraCrear([], [], [[self::D, 2, 0, 15]]);

        ob_start();
        $r = $p->getPermisoCrear(true);
        $salida = (string) ob_get_clean();

        $this->assertFalse($r);
        $this->assertStringContainsString('proceso', $salida);
    }

    public function test_crear_consulta_los_procesos_con_el_tipo_y_la_dl(): void
    {
        $this->instalarProcesos(true);
        $tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipos->expects($this->once())->method('getTiposDeProcesos')->with(self::TIPO, false)->willReturn([]);
        $p = (new P())->conRepositorios(actividad: $this->createMock(ActividadAllRepositoryInterface::class), tipoDeActividad: $tipos);
        $p->setId_tipo_activ(self::TIPO);

        ob_start();
        $p->getPermisoCrear(false);
        ob_end_clean();
    }

    // ---- serialización de sesión -----------------------------------------------------------------------------

    public function test_ida_y_vuelta_de_serializacion_conserva_estado_y_permisos(): void
    {
        $p = (new P())->conMatriz(['17....' => P::resto('17....', [[self::D, 10, 7, 3]])]);
        $this->actividadPropia($p, [10]);

        /** @var PermisosActividades $copia */
        $copia = unserialize(serialize($p));

        $this->assertEquals($p->estado(), $copia->__serialize());
        $copia->setFasesCompletadas([10]);
        $this->assertTrue($copia->getPermisoActual('datos')->have_perm_activ('modificar'));
    }

    public function test_unserialize_con_datos_vacios_deja_valores_por_defecto(): void
    {
        $p = new P();
        $p->__unserialize([]);

        $e = $p->estado();
        $this->assertSame(0, $e['idUsuario']);
        $this->assertSame([], $e['aPermDl']);
        $this->assertSame([], $e['aPermOtras']);
        $this->assertSame('', $e['sid_tipo_activ']);
        $this->assertSame(0, $e['iid_activ']);
        $this->assertFalse($e['bpropia']);
        $this->assertFalse($e['btop']);
        $this->assertSame([], $e['aFasesCompletadas']);
        $this->assertNull($e['setActividadContextTipo']);
    }

    public function test_unserialize_normaliza_los_tipos_de_la_sesion(): void
    {
        $p = new P();
        $p->__unserialize([
            'idUsuario' => '12',
            'sid_tipo_activ' => 17,
            'iid_activ' => '42',
            'iid_tipo_proceso' => 'x',
            'bpropia' => 1,
            'iid_fase' => '3',
            'btop' => 0,
            'aFasesCompletadas' => ['1', 2, 'x'],
            'setActividadContextTipo' => 171001,
            'setActividadContextDlOrg' => null,
        ]);

        $e = $p->estado();
        $this->assertSame(12, $e['idUsuario']);
        $this->assertSame('17', $e['sid_tipo_activ']);
        $this->assertSame(42, $e['iid_activ']);
        $this->assertSame(0, $e['iid_tipo_proceso']);
        $this->assertTrue($e['bpropia']);
        $this->assertSame(3, $e['iid_fase']);
        $this->assertFalse($e['btop']);
        $this->assertSame([1, 2, 0], $e['aFasesCompletadas']);
        $this->assertSame('171001', $e['setActividadContextTipo']);
        $this->assertNull($e['setActividadContextDlOrg']);
    }

    public function test_unserialize_funde_las_claves_alias_en_la_canonica_con_el_mayor_permiso(): void
    {
        $p = new P();
        $p->__unserialize([
            'aPermDl' => [
                '17' => P::resto('17', [[self::D, 10, 3, 31]]),
                '000017' => P::resto('000017', [[self::D, 10, 15, 7]]),
                '17....' => P::resto('17....', [[self::SACD, 4, 7, 7]]),
            ],
        ]);

        $dl = $p->estado()['aPermDl'];
        $this->assertSame(['17....'], array_keys($dl));
        /** @var XResto $resto */
        $resto = $dl['17....'];
        $this->assertSame(15, $resto->getPerm(self::D, 10, 'on'));
        $this->assertSame(31, $resto->getPerm(self::D, 10, 'off'));
        $this->assertTrue($resto->hasAfecta(self::SACD) || $resto->hasAfecta(self::D));
    }

    public function test_unserialize_descarta_lo_que_no_es_una_regla(): void
    {
        $p = new P();
        $p->__unserialize([
            'aPermDl' => [
                '17....' => 'no soy un XResto',
                '2.....' => P::resto('2.....', [[self::D, 1, 3, 3]]),
                '' => P::resto('', [[self::D, 1, 3, 3]]),
            ],
            'aPermOtras' => 'tampoco soy un array',
        ]);

        $this->assertSame(['2.....'], array_keys($p->estado()['aPermDl']));
        $this->assertSame([], $p->estado()['aPermOtras']);
    }

    public function test_unserialize_canonicaliza_las_claves_enteras(): void
    {
        $p = new P();
        $p->__unserialize(['aPermOtras' => [17 => P::resto('17', [[self::D, 10, 3, 3]])]]);

        $this->assertSame(['17....'], array_keys($p->estado()['aPermOtras']));
    }
}
