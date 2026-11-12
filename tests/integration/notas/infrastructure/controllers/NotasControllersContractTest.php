<?php

declare(strict_types=1);

namespace Tests\integration\notas\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\notas\application\ActaEliminar;
use src\notas\application\ActaImprimirPresentacionData;
use src\notas\application\ActaMarcarImpresa;
use src\notas\application\ActaModificar;
use src\notas\application\ActaNueva;
use src\notas\application\ActaPdfEliminar;
use src\notas\application\ActaPdfSubir;
use src\notas\application\ActaSelectData;
use src\notas\application\ActaVerAddPersona;
use src\notas\application\ActaVerAddPersonaFormData;
use src\notas\application\ActaVerFormData;
use src\notas\application\ActaVerNotasListadoData;
use src\notas\application\ActividadesBuscarData;
use src\notas\application\AsigFaltanPersonasSelectTablaData;
use src\notas\application\AsigFaltanSelectTablaData;
use src\notas\application\AsignaturasPendientesData;
use src\notas\application\AsignaturasPendientesResumenData;
use src\notas\application\AsignaturasSearchData;
use src\notas\application\BuscarActaData;
use src\notas\application\ComprobarNotasConstantsData;
use src\notas\application\ExaminadoresSearchData;
use src\notas\application\InformeStgrAgregados;
use src\notas\application\InformeStgrNumerarios;
use src\notas\application\InformeStgrProfesores;
use src\notas\application\ListadoAnualActasData;
use src\notas\application\NotaPersonaFormData;
use src\notas\application\PersonaNotaEditar;
use src\notas\application\PersonaNotaEliminar;
use src\notas\application\PersonaNotaNueva;
use src\notas\application\PosiblesOpcionalesData;
use src\notas\application\PosiblesPreceptoresData;
use src\notas\application\ResiduoOtraRegionComprobarData;
use src\notas\application\TesseraCopiar;
use src\notas\application\TesseraCopiarSelectData;
use src\notas\application\TesseraImprimirData;
use src\notas\application\TesseraVerData;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los controladores HTTP de `notas`: qué parte del request llega al caso de uso, el token de las
 * mutaciones y el envelope de `ContestarJson` (éxito, error del caso de uso, excepción).
 *
 * Los casos de uso son `final`: se sustituyen en el contenedor por dobles que apuntan las llamadas
 * (`DependencyResolver::get()` no comprueba el tipo).
 *
 * Fuera: `acta_pdf_download.php` (cabeceras + `exit()`) y la pantalla «comprobar notas» (`ComprobarNotasControllerTest`).
 */
final class NotasControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/notas/infrastructure/ui/http/controllers/';

    /** @var array<string, mixed> */
    private array $postAnterior = [];
    /** @var array<string, mixed> */
    private array $sesionAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
        $this->sesionAnterior = $_SESSION['session_auth'] ?? [];
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_POST = $this->postAnterior;
        $_SESSION['session_auth'] = $this->sesionAnterior;
        parent::tearDown();
    }

    /**
     * Doble que responde a cualquier método: devuelve `$respuestas[metodo]` (o lo lanza si es un `Throwable`) y
     * apunta las llamadas.
     *
     * @param array<string, mixed> $respuestas
     */
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
     */
    private function salida(string $controlador, array $post): string
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

        return $salida;
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function ejecutar(string $controlador, array $post = []): array
    {
        $salida = $this->salida($controlador, $post);
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));

        return $json;
    }

    /**
     * @param array<string, mixed> $json
     * @return array<string, mixed>
     */
    private static function data(array $json): array
    {
        $data = is_string($json['data'] ?? null) ? json_decode($json['data'], true) : ($json['data'] ?? null);

        return is_array($data) ? $data : [];
    }

    // ------------------------------------------------------------------ mutaciones con token

    /**
     * @return iterable<string, array{string, class-string, string, string}>
     */
    public static function mutacionesConTokenProvider(): iterable
    {
        yield 'acta nueva' => ['acta_nueva.php', ActaNueva::class, 'ctx_nuevo', 'acta_nueva'];
        yield 'acta modificar' => ['acta_modificar.php', ActaModificar::class, 'ctx_guardar', 'acta_modificar'];
        yield 'acta eliminar' => ['acta_eliminar.php', ActaEliminar::class, 'ctx_eliminar', 'acta_eliminar'];
        yield 'nota eliminar' => ['persona_nota_eliminar.php', PersonaNotaEliminar::class, 'ctx_eliminar', 'persona_nota_eliminar'];
    }

    #[DataProvider('mutacionesConTokenProvider')]
    public function test_mutacion_sin_token_valido_no_llama_al_caso_de_uso(string $controlador, string $clase, string $campo, string $accion): void
    {
        $caso = $this->doble($clase, ['execute' => '']);

        foreach (['sin token' => [], 'token falso' => [$campo => 'x'], 'token de otra acción' => [$campo => HashB::sign('otra_accion', [])]] as $caso_txt => $post) {
            $json = $this->ejecutar($controlador, $post);
            $this->assertFalse($json['success'], $caso_txt);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $caso_txt);
        }
        $this->assertSame([], $caso->llamadas);
    }

    #[DataProvider('mutacionesConTokenProvider')]
    public function test_mutacion_con_token_devuelve_el_error_del_caso_de_uso(string $controlador, string $clase, string $campo, string $accion): void
    {
        $this->doble($clase, ['execute' => 'no se puede']);

        $json = $this->ejecutar($controlador, [$campo => HashB::sign($accion, ['acta' => 'dlb 1/26'])]);

        $this->assertFalse($json['success']);
        $this->assertSame('no se puede', $json['mensaje']);
    }

    public function test_acta_modificar_toma_el_acta_del_token_y_no_del_post(): void
    {
        $caso = $this->doble(ActaModificar::class, ['execute' => '']);

        $json = $this->ejecutar('acta_modificar.php', [
            'ctx_guardar' => HashB::sign('acta_modificar', ['acta' => 'dlb 1/26']),
            'acta' => 'dlb 99/26',
            'f_acta' => '1/7/2026',
            'sel' => ['otra'],
        ]);

        $this->assertTrue($json['success']);
        $input = $caso->args('execute')[0];
        $this->assertSame('dlb 1/26', $input['acta']);
        $this->assertSame([], $input['sel']);
        $this->assertSame('1/7/2026', $input['f_acta'], 'el resto del formulario pasa tal cual');
    }

    public function test_acta_eliminar_toma_el_acta_del_token(): void
    {
        $caso = $this->doble(ActaEliminar::class, ['execute' => '']);

        $this->assertTrue($this->ejecutar('acta_eliminar.php', [
            'ctx_eliminar' => HashB::sign('acta_eliminar', ['acta' => 'dlb 1/26']),
            'acta' => 'dlb 99/26',
        ])['success']);

        $this->assertSame('dlb 1/26', $caso->args('execute')[0]['acta']);
    }

    public function test_acta_nueva_pasa_el_formulario(): void
    {
        $caso = $this->doble(ActaNueva::class, ['execute' => '']);

        $this->assertTrue($this->ejecutar('acta_nueva.php', [
            'ctx_nuevo' => HashB::sign('acta_nueva', []),
            'acta' => 'dlb 3/26',
        ])['success']);

        $this->assertSame('dlb 3/26', $caso->args('execute')[0]['acta']);
    }

    public function test_eliminar_nota_toma_persona_asignatura_y_nivel_del_token(): void
    {
        $caso = $this->doble(PersonaNotaEliminar::class, ['execute' => '']);

        $this->assertTrue($this->ejecutar('persona_nota_eliminar.php', [
            'ctx_eliminar' => HashB::sign('persona_nota_eliminar', ['id_nom' => 10011, 'id_nivel' => 1101, 'id_asignatura' => 1101, 'tipo_acta' => 1]),
            'id_pau' => '666',
            'id_asignatura' => '9999',
            'sel' => ['666#9999'],
        ])['success']);

        $input = $caso->args('execute')[0];
        $this->assertSame(10011, $input['id_pau']);
        $this->assertSame(1101, $input['id_nivel']);
        $this->assertSame(1101, $input['id_asignatura']);
        $this->assertSame(1, $input['tipo_acta']);
        $this->assertSame([], $input['sel']);
    }

    // ------------------------------------------------------------------ mutaciones sin token propio

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function notaNuevaEditarProvider(): iterable
    {
        yield 'nueva' => ['persona_nota_nueva.php', PersonaNotaNueva::class];
        yield 'editar' => ['persona_nota_editar.php', PersonaNotaEditar::class];
    }

    #[DataProvider('notaNuevaEditarProvider')]
    public function test_nota_nueva_o_editada_devuelve_mensaje_y_esquema(string $controlador, string $clase): void
    {
        $caso = $this->doble($clase, ['execute' => ['error' => '', 'mensaje' => 'guardado en H-dlbv', 'esquema' => 'H-dlbv']]);

        $json = $this->ejecutar($controlador, ['id_pau' => '10011', 'id_asignatura' => '1101']);

        $this->assertTrue($json['success']);
        $this->assertSame(['mensaje' => 'guardado en H-dlbv', 'esquema' => 'H-dlbv'], self::data($json));
        $this->assertSame('1101', $caso->args('execute')[0]['id_asignatura']);
    }

    #[DataProvider('notaNuevaEditarProvider')]
    public function test_nota_nueva_o_editada_con_error(string $controlador, string $clase): void
    {
        $this->doble($clase, ['execute' => ['error' => 'Ya existe esta nota', 'mensaje' => '', 'esquema' => '']]);

        $json = $this->ejecutar($controlador, []);

        $this->assertFalse($json['success']);
        $this->assertSame('Ya existe esta nota', $json['mensaje']);
    }

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function mutacionesSimplesProvider(): iterable
    {
        yield 'borrar pdf del acta' => ['acta_pdf_eliminar.php', ActaPdfEliminar::class];
        yield 'copiar tessera' => ['tessera_copiar.php', TesseraCopiar::class];
    }

    #[DataProvider('mutacionesSimplesProvider')]
    public function test_mutacion_simple_responde_ok_o_el_error(string $controlador, string $clase): void
    {
        $caso = $this->doble($clase, ['execute' => '']);
        $json = $this->ejecutar($controlador, ['acta' => 'dlb 1/26']);
        $this->assertTrue($json['success']);
        $this->assertSame('ok', $json['data']);
        $this->assertSame('dlb 1/26', $caso->args('execute')[0]['acta']);

        $this->doble($clase, ['execute' => 'fallo']);
        $json = $this->ejecutar($controlador, []);
        $this->assertFalse($json['success']);
        $this->assertSame('fallo', $json['mensaje']);
    }

    public function test_subir_pdf_del_acta_pasa_post_y_ficheros(): void
    {
        $filesAnterior = $_FILES;
        $_FILES = ['acta_pdf' => ['name' => 'a.pdf', 'tmp_name' => '/tmp/x', 'error' => UPLOAD_ERR_OK, 'size' => 3], 7 => 'basura'];
        try {
            $caso = $this->doble(ActaPdfSubir::class, ['execute' => ['error' => '', 'http_status' => 200]]);
            $json = $this->ejecutar('acta_pdf_subir.php', ['acta' => 'dlb 1/26']);
        } finally {
            $_FILES = $filesAnterior;
        }

        $this->assertTrue($json['success']);
        [$post, $files] = $caso->args('execute');
        $this->assertSame('dlb 1/26', $post['acta']);
        $this->assertSame(['acta_pdf'], array_keys($files), 'solo entradas de fichero con nombre');
    }

    public function test_subir_pdf_del_acta_con_error(): void
    {
        $this->doble(ActaPdfSubir::class, ['execute' => ['error' => 'demasiado grande', 'http_status' => 413]]);

        $json = $this->ejecutar('acta_pdf_subir.php', []);

        $this->assertFalse($json['success']);
        $this->assertSame('demasiado grande', $json['mensaje']);
    }

    public function test_anadir_persona_al_acta(): void
    {
        $this->doble(ActaVerAddPersona::class, ['execute' => ['success' => true, 'mensaje' => 'añadida']]);
        $json = $this->ejecutar('acta_ver_add_persona.php', []);
        $this->assertTrue($json['success']);
        $this->assertSame(['mensaje' => 'añadida'], self::data($json));

        $this->doble(ActaVerAddPersona::class, ['execute' => ['success' => false, 'mensaje' => 'ya está en el acta']]);
        $json = $this->ejecutar('acta_ver_add_persona.php', []);
        $this->assertFalse($json['success']);
        $this->assertSame('ya está en el acta', $json['mensaje']);

        $this->doble(ActaVerAddPersona::class, ['execute' => []]);
        $json = $this->ejecutar('acta_ver_add_persona.php', []);
        $this->assertFalse($json['success'], 'sin «success» cuenta como error');
        $this->assertSame('error', $json['mensaje']);
    }

    // ------------------------------------------------------------------ lecturas

    /**
     * Controladores de lectura que capturan cualquier excepción y la devuelven como `success=false`.
     *
     * @return iterable<string, array{string, class-string, string, array<string, mixed>}>
     */
    public static function lecturasConCapturaProvider(): iterable
    {
        yield 'acta select' => ['acta_select_data.php', ActaSelectData::class, 'execute', []];
        yield 'acta ver' => ['acta_ver_form_data.php', ActaVerFormData::class, 'execute', []];
        yield 'faltan personas' => ['asig_faltan_personas_select_data.php', AsigFaltanPersonasSelectTablaData::class, 'execute', []];
        yield 'faltan asignaturas' => ['asig_faltan_select_data.php', AsigFaltanSelectTablaData::class, 'execute', []];
        yield 'pendientes' => ['asignaturas_pendientes_data.php', AsignaturasPendientesData::class, 'execute', []];
        yield 'pendientes resumen' => ['asignaturas_pendientes_resumen_data.php', AsignaturasPendientesResumenData::class, 'execute', []];
        yield 'constantes' => ['comprobar_notas_constants_data.php', ComprobarNotasConstantsData::class, 'execute', []];
        yield 'informe agd' => ['informe_stgr_agd_data.php', InformeStgrAgregados::class, 'calcular', []];
        yield 'informe profesores' => ['informe_stgr_profesores_data.php', InformeStgrProfesores::class, 'calcular', []];
        yield 'tessera imprimir' => ['tessera_imprimir_data.php', TesseraImprimirData::class, 'execute', ['id_nom' => '10011']];
        yield 'tessera ver' => ['tessera_ver_data.php', TesseraVerData::class, 'execute', ['id_nom' => '10011']];
        yield 'tessera copiar select' => ['tessera_copiar_select_data.php', TesseraCopiarSelectData::class, 'execute', ['id_nom' => '10011']];
        yield 'listado anual' => ['acta_listado_anual_data.php', ListadoAnualActasData::class, 'execute', ['inicioIso' => '2025-10-01', 'finIso' => '2026-09-30']];
    }

    /**
     * @param class-string $clase
     * @param array<string, mixed> $post
     */
    #[DataProvider('lecturasConCapturaProvider')]
    public function test_lectura_devuelve_los_datos_del_caso_de_uso(string $controlador, string $clase, string $metodo, array $post): void
    {
        $this->doble($clase, [$metodo => ['filas' => [['a' => 1]]], 'resolverCeLugar' => '']);

        $json = $this->ejecutar($controlador, $post);

        $this->assertTrue($json['success'], $json['mensaje'] ?? '');
        $this->assertSame([['a' => 1]], self::data($json)['filas'] ?? self::data($json)['aActas']['filas'] ?? null);
    }

    /**
     * @param class-string $clase
     * @param array<string, mixed> $post
     */
    #[DataProvider('lecturasConCapturaProvider')]
    public function test_lectura_convierte_la_excepcion_en_error(string $controlador, string $clase, string $metodo, array $post): void
    {
        $this->doble($clase, [$metodo => new \RuntimeException('no hay conexión'), 'resolverCeLugar' => '']);

        $json = $this->ejecutar($controlador, $post);

        $this->assertFalse($json['success']);
        $this->assertSame('no hay conexión', $json['mensaje']);
    }

    public function test_tessera_ver_e_imprimir_pasan_el_id_como_entero(): void
    {
        $ver = $this->doble(TesseraVerData::class, ['execute' => []]);
        $imprimir = $this->doble(TesseraImprimirData::class, ['execute' => []]);

        $this->ejecutar('tessera_ver_data.php', ['id_nom' => '10011']);
        $this->ejecutar('tessera_imprimir_data.php', ['id_nom' => '10011']);

        $this->assertSame([10011], $ver->args('execute'));
        $this->assertSame([10011], $imprimir->args('execute'));
    }

    public function test_tessera_copiar_select_exige_persona(): void
    {
        $caso = $this->doble(TesseraCopiarSelectData::class, ['execute' => []]);

        $json = $this->ejecutar('tessera_copiar_select_data.php', ['id_nom' => '']);

        $this->assertFalse($json['success']);
        $this->assertSame('Se requiere id_nom', $json['mensaje']);
        $this->assertSame([], $caso->llamadas);
    }

    public function test_listado_anual_exige_las_dos_fechas_y_las_devuelve_en_formato_local(): void
    {
        $caso = $this->doble(ListadoAnualActasData::class, ['execute' => []]);

        $json = $this->ejecutar('acta_listado_anual_data.php', ['inicioIso' => '2025-10-01']);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('inicioIso y finIso', $json['mensaje']);
        $this->assertSame([], $caso->llamadas);

        $json = $this->ejecutar('acta_listado_anual_data.php', ['inicioIso' => '2025-10-01', 'finIso' => '2026-09-30']);
        $this->assertTrue($json['success']);
        $this->assertSame(['2025-10-01', '2026-09-30'], $caso->args('execute'));
        $data = self::data($json);
        $this->assertSame('1/10/2025', $data['inicio_local']);
        $this->assertSame('30/9/2026', $data['fin_local']);
    }

    public function test_acta_select_lee_titulo_acta_y_mes_de_fin_de_curso(): void
    {
        $caso = $this->doble(ActaSelectData::class, ['execute' => []]);

        $this->ejecutar('acta_select_data.php', ['titulo' => 'Actas', 'acta' => 'dlb']);
        $this->assertSame(['titulo' => 'Actas', 'acta' => 'dlb', 'mes_fin_stgr' => 6], $caso->args('execute')[0]);
    }

    public function test_asignaturas_que_faltan_normaliza_el_formulario(): void
    {
        $faltan = $this->doble(AsigFaltanSelectTablaData::class, ['execute' => []]);
        $personas = $this->doble(AsigFaltanPersonasSelectTablaData::class, ['execute' => []]);

        $this->ejecutar('asig_faltan_select_data.php', ['numero' => '3', 'personas_n' => 'n', 'lista' => '1', 'otro' => 'x']);
        $this->ejecutar('asig_faltan_personas_select_data.php', ['id_asignatura' => '1101', 'b_c' => 'c', 'c2' => '2']);

        $this->assertSame(
            ['numero' => 3, 'b_c' => '', 'c1' => '', 'c2' => '', 'personas_n' => 'n', 'personas_agd' => '', 'lista' => '1'],
            $faltan->args('execute')[0]
        );
        $this->assertSame(
            ['id_asignatura' => 1101, 'personas_n' => '', 'personas_agd' => '', 'b_c' => 'c', 'c1' => '', 'c2' => '2'],
            $personas->args('execute')[0]
        );
    }

    public function test_informe_numerarios_filtra_las_dl_y_resuelve_el_centro_de_estudios(): void
    {
        $informe = $this->doble(InformeStgrNumerarios::class, ['resolverCeLugar' => 'Barcelona', 'calcular' => ['total' => 4]]);

        $json = $this->ejecutar('informe_stgr_n_data.php', ['dl' => ['dlb', '', 'dlmE'], 'lista' => '1']);

        $this->assertTrue($json['success']);
        $this->assertSame([['dlb', 'dlmE']], $informe->args('resolverCeLugar'));
        $this->assertSame([['dlb', 'dlmE'], true, 'Barcelona'], $informe->args('calcular'));
        $this->assertSame(['total' => 4, 'ce_lugar' => 'Barcelona'], self::data($json));
    }

    public function test_informe_numerarios_sin_lista_y_con_error(): void
    {
        $informe = $this->doble(InformeStgrNumerarios::class, ['resolverCeLugar' => '', 'calcular' => []]);
        $this->ejecutar('informe_stgr_n_data.php', []);
        $this->assertSame([[], false, ''], $informe->args('calcular'));

        $this->doble(InformeStgrNumerarios::class, ['resolverCeLugar' => new \RuntimeException('sin centro')]);
        $json = $this->ejecutar('informe_stgr_n_data.php', []);
        $this->assertFalse($json['success']);
        $this->assertSame('sin centro', $json['mensaje']);
    }

    public function test_informes_agregados_y_profesores_leen_dl_y_lista(): void
    {
        $agd = $this->doble(InformeStgrAgregados::class, ['calcular' => []]);
        $prof = $this->doble(InformeStgrProfesores::class, ['calcular' => []]);

        $this->ejecutar('informe_stgr_agd_data.php', ['dl' => ['dlb', ''], 'lista' => '']);
        $this->ejecutar('informe_stgr_profesores_data.php', ['lista' => 'si']);

        $this->assertSame([['dlb'], false], $agd->args('calcular'));
        $this->assertSame([true], $prof->args('calcular'));
    }

    public function test_imprimir_acta_marca_la_impresion_y_acumula_su_error(): void
    {
        $datos = $this->doble(ActaImprimirPresentacionData::class, ['execute' => ['errores' => 'falta firma']]);
        $marca = $this->doble(ActaMarcarImpresa::class, ['execute' => 'no se pudo marcar']);

        $json = $this->ejecutar('acta_imprimir_presentacion_data.php', ['acta' => 'dlb 1/26', 'mode' => 'otro']);

        $this->assertTrue($json['success']);
        $this->assertSame(['dlb 1/26', 'imprimir'], $datos->args('execute'), 'un modo desconocido pasa a «imprimir»');
        $this->assertSame(['dlb 1/26'], $marca->args('execute'));
        $this->assertSame("falta firma\nno se pudo marcar", self::data($json)['errores']);
    }

    public function test_imprimir_acta_en_mpdf_sin_errores(): void
    {
        $datos = $this->doble(ActaImprimirPresentacionData::class, ['execute' => ['acta' => 'dlb 1/26']]);
        $this->doble(ActaMarcarImpresa::class, ['execute' => '']);

        $json = $this->ejecutar('acta_imprimir_presentacion_data.php', ['acta' => 'dlb 1/26', 'mode' => 'mpdf']);

        $this->assertSame(['dlb 1/26', 'mpdf'], $datos->args('execute'));
        $this->assertSame(['acta' => 'dlb 1/26'], self::data($json), 'sin «errores» si no los hay');
    }

    public function test_formulario_para_anadir_persona_al_acta(): void
    {
        $this->doble(ActaVerAddPersonaFormData::class, ['execute' => ['error' => 'acta cerrada', 'puede_anadir' => false]]);
        $json = $this->ejecutar('acta_ver_add_persona_form_data.php', []);
        $this->assertFalse($json['success']);
        $this->assertSame('acta cerrada', $json['mensaje']);

        $this->doble(ActaVerAddPersonaFormData::class, ['execute' => ['error' => 'aviso', 'puede_anadir' => true, 'personas' => [1]]]);
        $json = $this->ejecutar('acta_ver_add_persona_form_data.php', []);
        $this->assertTrue($json['success'], 'si puede añadir, el error es solo un aviso dentro de los datos');
        $this->assertSame([1], self::data($json)['personas']);
    }

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function lecturasSinCapturaProvider(): iterable
    {
        yield 'notas del acta' => ['acta_ver_notas_listado_data.php', ActaVerNotasListadoData::class];
        yield 'buscar actividades' => ['actividades_buscar_data.php', ActividadesBuscarData::class];
        yield 'buscar acta' => ['buscar_acta.php', BuscarActaData::class];
    }

    /**
     * @param class-string $clase
     */
    #[DataProvider('lecturasSinCapturaProvider')]
    public function test_lectura_sin_captura_pasa_el_post(string $controlador, string $clase): void
    {
        $caso = $this->doble($clase, ['execute' => ['x' => 1]]);

        $json = $this->ejecutar($controlador, ['acta' => 'dlb']);

        $this->assertTrue($json['success']);
        $this->assertSame(['x' => 1], self::data($json));
        $this->assertSame(['acta' => 'dlb'], $caso->args('execute')[0]);
    }

    public function test_formulario_de_nota_junta_datos_ayudas_y_valores_de_los_vo(): void
    {
        $caso = $this->doble(NotaPersonaFormData::class, [
            'execute' => ['id_pau' => 10011],
            'opcionalesGenericasHelpers' => ['h' => 1],
            'formNotasVoPack' => ['a_situaciones' => [1 => 'superada']],
        ]);

        $json = $this->ejecutar('nota_persona_form_data.php', ['id_pau' => '10011', 'sel' => ['10011#1101'], 'mod' => 'editar']);

        $this->assertSame(['id_pau' => 10011, 'helpers' => ['h' => 1], 'a_situaciones' => [1 => 'superada']], self::data($json));
        $this->assertSame(
            ['id_pau' => 10011, 'id_asignatura_real' => '', 'sel' => ['10011#1101'], 'pau' => '', 'mod' => 'editar'],
            $caso->args('execute')[0]
        );
        $this->assertSame([10011], $caso->args('opcionalesGenericasHelpers'));
    }

    public function test_desplegables_de_opcionales_y_preceptores_mantienen_el_orden(): void
    {
        $this->doble(PosiblesOpcionalesData::class, ['execute' => [1230 => 'Zeta', 1201 => 'Alfa']]);
        $this->doble(PosiblesPreceptoresData::class, ['execute' => [900 => 'Zapata', 100 => 'Abad']]);

        $opc = self::data($this->ejecutar('posibles_opcionales_data.php', []));
        $pre = self::data($this->ejecutar('posibles_preceptores_data.php', []));

        $this->assertSame('id_asignatura', $opc['id']);
        $this->assertSame([['1230', 'Zeta'], ['1201', 'Alfa']], $opc['opciones']);
        $this->assertTrue($opc['blanco']);
        $this->assertSame('id_preceptor', $pre['id']);
        $this->assertSame([['900', 'Zapata'], ['100', 'Abad']], $pre['opciones']);
    }

    public function test_autocompletados_devuelven_el_json_del_caso_de_uso_tal_cual(): void
    {
        $this->doble(AsignaturasSearchData::class, ['execute' => '[{"id":1101}]']);
        $this->doble(ExaminadoresSearchData::class, ['execute' => '["Pérez"]']);

        $this->assertSame('[{"id":1101}]', $this->salida('asignaturas_search.php', ['q' => 'fil']));
        $this->assertSame('["Pérez"]', $this->salida('examinadores_search.php', ['q' => 'pé']));
    }

    public function test_residuo_de_otra_region_solo_para_roles_1_y_2(): void
    {
        $caso = $this->doble(ResiduoOtraRegionComprobarData::class, ['execute' => ['filas' => []]]);

        $_SESSION['session_auth']['id_role'] = 5;
        $json = $this->ejecutar('residuo_otra_region_stgr_data.php');
        $this->assertFalse($json['success']);
        $this->assertSame('No tiene permiso para ver el residuo de notas.', $json['mensaje']);
        $this->assertSame([], $caso->llamadas);

        foreach ([1, 2] as $rol) {
            $_SESSION['session_auth']['id_role'] = $rol;
            $this->assertTrue($this->ejecutar('residuo_otra_region_stgr_data.php')['success'], "rol $rol");
        }

        $this->doble(ResiduoOtraRegionComprobarData::class, ['execute' => new \RuntimeException('falla')]);
        $json = $this->ejecutar('residuo_otra_region_stgr_data.php');
        $this->assertSame('falla', $json['mensaje']);
    }
}
