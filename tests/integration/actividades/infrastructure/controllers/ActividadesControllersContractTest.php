<?php

declare(strict_types=1);

namespace Tests\integration\actividades\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\actividades\application\ActividadCambiarTipo;
use src\actividades\application\ActividadDuplicar;
use src\actividades\application\ActividadEditar;
use src\actividades\application\ActividadEliminar;
use src\actividades\application\ActividadFaseCompletadaDatos;
use src\actividades\application\ActividadFasesCompletadasDatos;
use src\actividades\application\ActividadImportar;
use src\actividades\application\ActividadNueva;
use src\actividades\application\ActividadNuevoCursoEjecutar;
use src\actividades\application\ActividadNuevoCursoFormData;
use src\actividades\application\ActividadPublicar;
use src\actividades\application\ActividadQueDatos;
use src\actividades\application\ActividadSelectListado;
use src\actividades\application\ActividadSelectUbiData;
use src\actividades\application\ActividadStatusLabelsDatos;
use src\actividades\application\ActividadTipoGetAsistentes;
use src\actividades\application\ActividadTipoGetIdTarifa;
use src\actividades\application\ActividadVerDatos;
use src\actividades\application\CalendarioListasDatos;
use src\actividades\application\ListaActividadesSgListado;
use src\actividades\application\ListaActivTabla;
use src\actividades\application\ListaCentrosActivDatos;
use src\actividades\application\ListaSrCsvListado;
use src\actividades\application\ListaSrCsvQueDatos;
use src\actividades\application\TipoActivEliminar;
use src\actividades\application\TipoActivFormModificar;
use src\actividades\application\TipoActivFormNuevo;
use src\actividades\application\TipoActivLista;
use src\actividades\application\TipoActivMetadata;
use src\actividades\application\TipoActivNuevo;
use src\actividades\application\TipoActivUpdate;
use src\permisos\domain\PermisosActividades;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los controladores HTTP de `actividades`: token de las mutaciones
 * (cápsula propia o una por fila en `sel[]`), qué parte del request llega al caso
 * de uso, envelope de `ContestarJson`, y que ningún controlador corte la
 * petición con `exit` (en proceso cortaría al controlador frontend que llama).
 *
 * Los casos de uso se sustituyen en el contenedor por dobles que apuntan las llamadas.
 */
final class ActividadesControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/actividades/infrastructure/ui/http/controllers/';

    /** @var array<string, mixed> */
    private array $postAnterior = [];
    private mixed $permAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
        $this->permAnterior = $_SESSION['oPermActividades'] ?? null;
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_POST = $this->postAnterior;
        $_SESSION['oPermActividades'] = $this->permAnterior;
        parent::tearDown();
    }

    /** @param array<string, mixed> $respuestas */
    private function doble(string $id, array $respuestas): object
    {
        $doble = new class($respuestas) {
            /** @var list<array{string, array<int, mixed>}> */
            public array $llamadas = [];

            /** @param array<string, mixed> $respuestas */
            public function __construct(private array $respuestas)
            {
            }

            /** @param array<int, mixed> $args */
            public function __call(string $metodo, array $args): mixed
            {
                $this->llamadas[] = [$metodo, $args];
                $r = $this->respuestas[$metodo] ?? null;
                if ($r instanceof \Throwable) {
                    throw $r;
                }
                return $r;
            }

            /** @return array<int, mixed> */
            public function args(string $metodo): array
            {
                foreach ($this->llamadas as [$m, $a]) {
                    if ($m === $metodo) {
                        return $a;
                    }
                }
                throw new \LogicException("No se llamó a $metodo");
            }
        };
        $this->sustituirServicio($id, $doble);
        return $doble;
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function ejecutar(string $controlador, array $post = []): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::DIR . $controlador;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));
        return $json;
    }

    /** @return array<string, mixed> */
    private static function data(array $json): array
    {
        $data = is_string($json['data'] ?? null) ? json_decode($json['data'], true) : ($json['data'] ?? null);
        return is_array($data) ? $data : [];
    }

    private function noAutorizado(array $json, string $caso = ''): void
    {
        $this->assertFalse($json['success'], $caso);
        $this->assertSame('Operación no autorizada', $json['mensaje'], $caso);
    }

    // ------------------------------------------------------------ mutaciones con cápsula propia

    /** @return iterable<string, array{string, class-string, string, string, string, mixed}> */
    public static function mutacionesConTokenProvider(): iterable
    {
        yield 'cambiar tipo' => ['actividad_cambiar_tipo.php', ActividadCambiarTipo::class, 'execute', 'ctx_cambiar_tipo', 'actividad_cambiar_tipo', ['error_txt' => '']];
        yield 'editar' => ['actividad_editar.php', ActividadEditar::class, 'execute', 'ctx_editar', 'actividad_editar', ['error_txt' => '']];
        yield 'nueva' => ['actividad_nuevo.php', ActividadNueva::class, 'actividadNueva', 'ctx_nuevo', 'actividad_nuevo', '3001'];
        yield 'nuevo curso' => ['actividad_nuevo_curso_ejecutar.php', ActividadNuevoCursoEjecutar::class, 'ejecutar', 'ctx_ejecutar', 'actividad_nuevo_curso_ejecutar', ['html' => '', 'copiadas' => 0]];
        yield 'tipo nuevo' => ['tipo_activ_nuevo.php', TipoActivNuevo::class, 'execute', 'ctx_nuevo', 'tipo_activ_nuevo', ''];
        yield 'tipo update' => ['tipo_activ_update.php', TipoActivUpdate::class, 'execute', 'ctx_guardar', 'tipo_activ_update', ''];
        yield 'tipo eliminar' => ['tipo_activ_eliminar.php', TipoActivEliminar::class, 'execute', 'ctx_eliminar', 'tipo_activ_eliminar', ''];
    }

    #[DataProvider('mutacionesConTokenProvider')]
    public function test_mutacion_sin_token_valido_no_llama_al_caso_de_uso(string $ctrl, string $clase, string $metodo, string $campo, string $accion, mixed $ok): void
    {
        $caso = $this->doble($clase, [$metodo => $ok]);
        foreach (['sin token' => [], 'token falso' => [$campo => 'x'], 'token de otra acción' => [$campo => HashB::sign('otra_accion', [])]] as $txt => $post) {
            $this->noAutorizado($this->ejecutar($ctrl, $post + ['inom_tipo_val' => 'x']), $txt);
        }
        $this->assertSame([], $caso->llamadas);
    }

    #[DataProvider('mutacionesConTokenProvider')]
    public function test_mutacion_con_token_responde_exito(string $ctrl, string $clase, string $metodo, string $campo, string $accion, mixed $ok): void
    {
        $caso = $this->doble($clase, [$metodo => $ok]);
        $json = $this->ejecutar($ctrl, [$campo => HashB::sign($accion, ['id_activ' => 3001, 'id_tipo_activ' => 112021]), 'inom_tipo_val' => '021']);
        $this->assertTrue($json['success']);
        $this->assertCount(1, $caso->llamadas);
    }

    public function test_editar_y_cambiar_tipo_toman_la_actividad_del_token(): void
    {
        foreach ([['actividad_editar.php', ActividadEditar::class, 'ctx_editar', 'actividad_editar'], ['actividad_cambiar_tipo.php', ActividadCambiarTipo::class, 'ctx_cambiar_tipo', 'actividad_cambiar_tipo']] as [$ctrl, $clase, $campo, $accion]) {
            $caso = $this->doble($clase, ['execute' => ['error_txt' => '']]);
            $this->ejecutar($ctrl, [$campo => HashB::sign($accion, ['id_activ' => 3001]), 'id_activ' => '666', 'nom_activ' => 'cv']);
            $input = $caso->args('execute')[0];
            $this->assertSame(3001, $input['id_activ'], $ctrl);
            $this->assertSame('cv', $input['nom_activ'], "$ctrl: el resto del formulario pasa tal cual");
        }
    }

    public function test_editar_y_cambiar_tipo_devuelven_el_error_del_caso_de_uso_sin_cortar_la_peticion(): void
    {
        foreach ([['actividad_editar.php', ActividadEditar::class, 'ctx_editar', 'actividad_editar'], ['actividad_cambiar_tipo.php', ActividadCambiarTipo::class, 'ctx_cambiar_tipo', 'actividad_cambiar_tipo']] as [$ctrl, $clase, $campo, $accion]) {
            foreach ([['error_txt' => 'sin permiso', 'tipo_error' => 'permiso'], ['error_txt' => 'no guardado']] as $res) {
                $this->doble($clase, ['execute' => $res]);
                $json = $this->ejecutar($ctrl, [$campo => HashB::sign($accion, ['id_activ' => 3001])]);
                $this->assertFalse($json['success'], $ctrl);
                $this->assertSame($res['error_txt'], $json['mensaje'], $ctrl);
            }
        }
    }

    public function test_tipo_activ_toma_el_tipo_del_token_y_devuelve_el_mensaje(): void
    {
        foreach ([['tipo_activ_update.php', TipoActivUpdate::class, 'ctx_guardar', 'tipo_activ_update'], ['tipo_activ_eliminar.php', TipoActivEliminar::class, 'ctx_eliminar', 'tipo_activ_eliminar']] as [$ctrl, $clase, $campo, $accion]) {
            $caso = $this->doble($clase, ['execute' => 'hecho']);
            $json = $this->ejecutar($ctrl, [$campo => HashB::sign($accion, ['id_tipo_activ' => 112021]), 'id_tipo_activ' => '999999']);
            $this->assertSame(112021, $caso->args('execute')[0]['id_tipo_activ'], $ctrl);
            $this->assertSame(['mensaje' => 'hecho'], self::data($json), $ctrl);
        }
    }

    // ------------------------------------------------------------ alta de actividad

    private function nueva(array $post): array
    {
        return $this->ejecutar('actividad_nuevo.php', $post + ['ctx_nuevo' => HashB::sign('actividad_nuevo', [])]);
    }

    public function test_nueva_sin_tipo_avisa_sin_cortar_la_peticion(): void
    {
        $caso = $this->doble(ActividadNueva::class, ['actividadNueva' => '1']);
        $json = $this->nueva(['inom_tipo_val' => '']);
        $this->assertFalse($json['success']);
        $this->assertSame('debe seleccionar un tipo de actividad', $json['mensaje']);
        $this->assertSame([], $caso->llamadas);
    }

    public function test_nueva_pasa_el_formulario_con_el_precio_decimal_intacto(): void
    {
        $caso = $this->doble(ActividadNueva::class, ['actividadNueva' => '3001']);
        $this->nueva([
            'inom_tipo_val' => '021', 'id_tipo_activ' => '112021', 'id_ubi' => '200', 'status' => '2',
            'id_tarifa' => '4', 'precio' => '350,50', 'dl_org' => 'dlb', 'nom_activ' => 'ca', 'f_ini' => '1/7/2027',
            'publicado' => 'true',
        ]);
        $d = $caso->args('actividadNueva')[0];
        $this->assertSame([112021, 200, 2, 4, 'dlb', 'ca', '1/7/2027', 'true'], [$d['id_tipo_activ'], $d['id_ubi'], $d['status'], $d['tarifa'], $d['dl_org'], $d['nom_activ'], $d['f_ini'], $d['publicado']]);
        $this->assertSame('350,50', $d['precio'], 'antes FILTER_SANITIZE_NUMBER_FLOAT dejaba «35050»');
    }

    public function test_nueva_devuelve_el_mensaje_de_la_excepcion(): void
    {
        $this->doble(ActividadNueva::class, ['actividadNueva' => new \RuntimeException('tipo de actividad incorrecto')]);
        $json = $this->nueva(['inom_tipo_val' => '021']);
        $this->assertFalse($json['success']);
        $this->assertSame('tipo de actividad incorrecto', $json['mensaje']);
    }

    public function test_nuevo_curso_pasa_los_anios_y_devuelve_el_resultado(): void
    {
        $caso = $this->doble(ActividadNuevoCursoEjecutar::class, ['ejecutar' => ['html' => '<h3>2</h3>', 'copiadas' => 2]]);
        $json = $this->ejecutar('actividad_nuevo_curso_ejecutar.php', [
            'ctx_ejecutar' => HashB::sign('actividad_nuevo_curso_ejecutar', []), 'year_ref' => '2026', 'year' => '2027', 'ver_lista' => 't',
        ]);
        $this->assertSame(['year_ref' => 2026, 'year' => 2027, 'ver_lista' => 't'], $caso->args('ejecutar')[0]);
        $this->assertSame(['html' => '<h3>2</h3>', 'copiadas' => 2], self::data($json));
    }

    // ------------------------------------------------------------ mutaciones con cápsula por fila

    /** @return iterable<string, array{string, class-string, string, mixed}> */
    public static function mutacionesPorFilaProvider(): iterable
    {
        yield 'duplicar' => ['actividad_duplicar.php', ActividadDuplicar::class, 'actividad_duplicar', ''];
        yield 'eliminar' => ['actividad_eliminar.php', ActividadEliminar::class, 'actividad_eliminar', ''];
        yield 'importar' => ['actividad_importar.php', ActividadImportar::class, 'actividad_importar', ['error_txt' => '', 'avisos' => []]];
        yield 'publicar' => ['actividad_publicar.php', ActividadPublicar::class, 'actividad_publicar', ''];
    }

    #[DataProvider('mutacionesPorFilaProvider')]
    public function test_seleccion_con_capsulas_falsas_no_llama_al_caso_de_uso(string $ctrl, string $clase, string $accion, mixed $ok): void
    {
        $caso = $this->doble($clase, ['execute' => $ok]);
        $this->noAutorizado($this->ejecutar($ctrl, ['sel' => ['3001#cv', HashB::sign('otra_accion', ['id_activ' => 3002])]]));
        $this->assertSame([], $caso->llamadas);
    }

    #[DataProvider('mutacionesPorFilaProvider')]
    public function test_seleccion_pasa_solo_los_ids_de_las_capsulas_validas(string $ctrl, string $clase, string $accion, mixed $ok): void
    {
        $caso = $this->doble($clase, ['execute' => $ok]);
        $json = $this->ejecutar($ctrl, ['sel' => [
            HashB::sign($accion, ['id_activ' => 3001]),
            'falsa',
            HashB::sign($accion, ['id_activ' => 3002]),
        ]]);
        $this->assertTrue($json['success']);
        $this->assertSame(['3001', '3002'], $caso->args('execute')[0]['sel']);
    }

    public function test_duplicar_sin_seleccion_no_esta_autorizado(): void
    {
        $caso = $this->doble(ActividadDuplicar::class, ['execute' => '']);
        $this->noAutorizado($this->ejecutar('actividad_duplicar.php', []));
        $this->assertSame([], $caso->llamadas);
    }

    public function test_importar_con_avisos_los_devuelve(): void
    {
        $this->doble(ActividadImportar::class, ['execute' => ['error_txt' => '', 'avisos' => ['sin proceso']]]);
        $json = $this->ejecutar('actividad_importar.php', ['sel' => [HashB::sign('actividad_importar', ['id_activ' => 3001])]]);
        $this->assertTrue($json['success']);
        $this->assertSame(['avisos' => ['sin proceso']], self::data($json));
    }

    public function test_mutacion_por_fila_devuelve_el_error_del_caso_de_uso(): void
    {
        $this->doble(ActividadPublicar::class, ['execute' => 'no se puede publicar']);
        $json = $this->ejecutar('actividad_publicar.php', ['sel' => [HashB::sign('actividad_publicar', ['id_activ' => 3001])]]);
        $this->assertFalse($json['success']);
        $this->assertSame('no se puede publicar', $json['mensaje']);
    }

    // ------------------------------------------------------------ lecturas

    public function test_fases_completadas_leen_post_o_get(): void
    {
        $caso = $this->doble(ActividadFasesCompletadasDatos::class, ['ejecutar' => ['fases_completadas' => [1, 2]]]);
        $json = $this->ejecutar('actividad_fases_completadas_datos.php', ['id_activ' => '3001']);
        $this->assertSame([3001], $caso->args('ejecutar'));
        $this->assertSame(['fases_completadas' => [1, 2]], self::data($json));

        $caso = $this->doble(ActividadFaseCompletadaDatos::class, ['ejecutar' => ['completada' => true]]);
        $this->ejecutar('actividad_fase_completada_datos.php', ['id_activ' => '3001', 'id_fase' => '5']);
        $this->assertSame([3001, 5], $caso->args('ejecutar'));
    }

    public function test_listados_pasan_los_filtros_al_caso_de_uso(): void
    {
        $caso = $this->doble(ActividadSelectListado::class, ['ejecutar' => ['a_valores' => []]]);
        $this->ejecutar('actividad_select_datos.php', ['modo' => 'buscar', 'status' => '2', 'id_ubi' => '200', 'stack_go' => '3', 'sel' => ['1#a']]);
        [$input, $stack] = $caso->args('ejecutar');
        $this->assertSame(['buscar', 2, 200, ['1#a'], 3], [$input['modo'], $input['status'], $input['id_ubi'], $input['sel'], $stack]);

        $caso = $this->doble(ListaActividadesSgListado::class, ['ejecutar' => []]);
        $this->ejecutar('lista_actividades_sg_datos.php', ['tipo_activ_sg' => 'crt', 'year' => '2027']);
        $this->assertSame(['crt', '2027'], [$caso->args('ejecutar')[0]['tipo_activ_sg'], $caso->args('ejecutar')[0]['year']]);

        $caso = $this->doble(CalendarioListasDatos::class, ['ejecutar' => []]);
        $this->ejecutar('calendario_listas_datos.php', ['que' => 'lista_cdc', 'id_cdc' => ['200', '201']]);
        $this->assertSame(['lista_cdc', ['200', '201']], [$caso->args('ejecutar')[0]['que'], $caso->args('ejecutar')[0]['id_cdc']]);

        $caso = $this->doble(ListaCentrosActivDatos::class, ['ejecutar' => []]);
        $this->ejecutar('lista_centros_activ_datos.php', ['id_ctr_num' => '3', 'id_ctr' => ['1']]);
        $this->assertSame([3, ['1']], [$caso->args('ejecutar')[0]['id_ctr_num'], $caso->args('ejecutar')[0]['id_ctr']]);

        $caso = $this->doble(ListaSrCsvListado::class, ['ejecutar' => []]);
        $this->ejecutar('lista_sr_csv_datos.php', ['status' => ['2', '3'], 'c_activ' => ['crt']]);
        $this->assertSame([['2', '3'], ['crt']], [$caso->args('ejecutar')[0]['status'], $caso->args('ejecutar')[0]['c_activ']]);

        $caso = $this->doble(ListaActivTabla::class, ['execute' => ['ok' => 1]]);
        $json = $this->ejecutar('lista_activ_datos.php', ['que' => 'list_activ', 'seccion' => ['1']]);
        [$input, $opts] = $caso->args('execute');
        $this->assertSame(['list_activ', ['1']], [$input['que'], $input['seccion']]);
        $this->assertSame(['mi_sfsv', 'perm_vcsd', 'perm_des', 'perm_sg', 'perm_admin', 'is_dmz'], array_keys($opts));
        $this->assertSame(['ok' => 1], self::data($json));
    }

    public function test_lista_activ_pasa_uno_o_varios_estados(): void
    {
        $caso = $this->doble(ListaActivTabla::class, ['execute' => []]);
        $this->ejecutar('lista_activ_datos.php', ['status' => ['1', '2']]);
        $this->ejecutar('lista_activ_datos.php', ['status' => '2']);
        // Antes, con varias casillas, filter_var sin FILTER_REQUIRE_ARRAY daba false: listado sin filtrar por estado.
        $this->assertSame([['1', '2'], '2'], array_map(fn ($l) => $l[1][0]['status'], $caso->llamadas));
    }

    public function test_ver_datos_calcula_el_nivel_stgr_por_defecto_si_no_viene(): void
    {
        $caso = $this->doble(ActividadVerDatos::class, ['ejecutar' => []]);
        $this->ejecutar('actividad_ver_datos.php', ['id_activ' => '3001', 'id_tipo_activ' => '112021', 'calc_tarifa_inicial' => '1']);
        $input = $caso->args('ejecutar')[0];
        $this->assertSame([3001, 't', true], [$input['id_activ'], $input['Bdl'], $input['calc_tarifa_inicial']]);
        $this->assertSame(ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad('112021'), $input['nivel_stgr']);

        $this->ejecutar('actividad_ver_datos.php', ['id_activ' => '3001', 'nivel_stgr' => '5', 'Bdl' => 'f']);
        $input = $caso->args('ejecutar')[0];
        $this->assertSame('5', $caso->llamadas[1][1][0]['nivel_stgr']);
        $this->assertSame('f', $caso->llamadas[1][1][0]['Bdl']);
    }

    public function test_que_datos_pasa_los_flags_como_booleanos(): void
    {
        $caso = $this->doble(ActividadQueDatos::class, ['execute' => []]);
        $this->ejecutar('actividad_que_datos.php', ['perm_jefe' => 't', 'extendida' => 'f', 'sfsv_all' => 't', 'que' => 'x']);
        $in = $caso->args('execute')[0];
        $this->assertSame([true, false, true, 'x'], [$in['perm_jefe'], $in['extendida'], $in['sfsv_all'], $in['que']]);
        $this->ejecutar('actividad_que_datos.php', []);
        $this->assertArrayNotHasKey('sfsv_all', $caso->llamadas[1][1][0]);
    }

    public function test_lecturas_simples_devuelven_el_payload(): void
    {
        foreach ([
            ['actividad_nuevo_curso_form_data.php', ActividadNuevoCursoFormData::class, 'execute'],
            ['actividad_status_labels_datos.php', ActividadStatusLabelsDatos::class, 'execute'],
            ['lista_sr_csv_que_datos.php', ListaSrCsvQueDatos::class, 'ejecutar'],
            ['tipo_activ_metadata.php', TipoActivMetadata::class, 'execute'],
        ] as [$ctrl, $clase, $metodo]) {
            $this->doble($clase, [$metodo => ['k' => 'v']]);
            $json = $this->ejecutar($ctrl, ['with_all' => 't']);
            $this->assertTrue($json['success'], $ctrl);
            $this->assertSame(['k' => 'v'], self::data($json), $ctrl);
        }
        foreach ([['tipo_activ_lista.php', TipoActivLista::class], ['tipo_activ_form_nuevo.php', TipoActivFormNuevo::class], ['tipo_activ_form_modificar.php', TipoActivFormModificar::class]] as [$ctrl, $clase]) {
            $this->doble($clase, ['execute' => '<table></table>']);
            $this->assertSame(['html' => '<table></table>'], self::data($this->ejecutar($ctrl)), $ctrl);
        }
    }

    public function test_nivel_stgr_por_defecto(): void
    {
        $json = $this->ejecutar('actividad_nivel_stgr_default_datos.php', ['id_tipo_activ' => '112021']);
        $this->assertSame(['nivel_stgr_default' => ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad('112021')], self::data($json));
    }

    // ------------------------------------------------------------ desplegables y despachadores

    public function test_desplegable_de_ubis_frecuentes_y_de_region(): void
    {
        $caso = $this->doble(ActividadSelectUbiData::class, ['execute' => ['opcionesFreq' => [['200', 'Casa A']], 'opcionesRegion' => [['dl|dlb', 'dlb']]]]);
        $freq = self::data($this->ejecutar('actividad_select_ubi_desplegable.php', ['tipo' => 'freq', 'dl_org' => 'dlb', 'isfsv' => '1']));
        $this->assertSame(['dl_org' => 'dlb', 'isfsv' => 1], $caso->args('execute')[0]);
        $this->assertSame(['id_ubi_1', [['200', 'Casa A']], ''], [$freq['id'], $freq['opciones'], $freq['mensaje']]);

        $sinDl = self::data($this->ejecutar('actividad_select_ubi_desplegable.php', ['tipo' => 'freq']));
        $this->assertSame([[], 'falta saber quien organiza'], [$sinDl['opciones'], $sinDl['mensaje']]);

        $region = self::data($this->ejecutar('actividad_select_ubi_desplegable.php', ['tipo' => 'region', 'dl_org' => 'dlb']));
        $this->assertSame(['filtro_lugar', [['dl|dlb', 'dlb']], 'dl|dlb', 'fnjs_lugar()'], [$region['id'], $region['opciones'], $region['selected'], $region['action']]);
    }

    public function test_desplegable_de_ubis_con_tipo_desconocido_avisa_sin_cortar_la_peticion(): void
    {
        $json = $this->ejecutar('actividad_select_ubi_desplegable.php', ['tipo' => 'otro']);
        $this->assertFalse($json['success']);
        $this->assertSame('opción no definida: tipo=otro', $json['mensaje']);
    }

    public function test_tipo_get_despacha_por_salida(): void
    {
        $caso = $this->doble(ActividadTipoGetAsistentes::class, ['execute' => ['opciones' => [['1', 'n']]]]);
        $json = $this->ejecutar('actividad_tipo_get.php', ['salida' => 'asistentes', 'sfsv' => '1']);
        $this->assertSame('1', $caso->args('execute')[0]['sfsv']);
        $this->assertSame(['opciones' => [['1', 'n']]], self::data($json));

        $this->doble(ActividadTipoGetIdTarifa::class, ['execute' => 4]);
        $this->assertSame(['content' => '4'], self::data($this->ejecutar('actividad_tipo_get.php', ['salida' => 'id_tarifa'])));
    }

    public function test_tipo_get_con_salida_desconocida_avisa_sin_cortar_la_peticion(): void
    {
        $json = $this->ejecutar('actividad_tipo_get.php', ['salida' => 'otra']);
        $this->assertFalse($json['success']);
        $this->assertSame('opción no definida: salida=otra', $json['mensaje']);
    }

    public function test_permiso_crear(): void
    {
        unset($_SESSION['oPermActividades']);
        $json = $this->ejecutar('actividad_permiso_crear_datos.php', ['id_tipo_activ' => '112021']);
        $this->assertFalse($json['success']);
        $this->assertSame('Sesión sin permisos de actividades', $json['mensaje']);

        $perm = $this->createMock(PermisosActividades::class);
        $perm->expects($this->once())->method('setId_tipo_activ')->with('112021');
        $perm->expects($this->once())->method('getPermisoCrear')->with(false)->willReturnCallback(function (): false {
            echo ' debería crear un proceso ';
            return false;
        });
        $_SESSION['oPermActividades'] = $perm;
        $json = $this->ejecutar('actividad_permiso_crear_datos.php', ['id_tipo_activ' => '112021', 'dl_propia' => 'f']);
        $this->assertSame(['permiso_crear' => false, 'aviso' => 'debería crear un proceso'], self::data($json));
    }
}
