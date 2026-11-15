<?php

declare(strict_types=1);

namespace Tests\integration\asistentes\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\asistentes\application\ActivPendientesSelectData;
use src\asistentes\application\AsistenteEliminar;
use src\asistentes\application\AsistenteGuardar;
use src\asistentes\application\AsistenteMoverData;
use src\asistentes\application\AsistentePlazaAsignar;
use src\asistentes\application\FormActividadesDeUnaPersonaData;
use src\asistentes\application\FormAsistentesAUnaActividadData;
use src\asistentes\application\ListaActivCtrData;
use src\asistentes\application\ListaAsisConjuntoActivData;
use src\asistentes\application\ListaAsistentesData;
use src\asistentes\application\ListaEstCtrData;
use src\asistentes\application\ListaUltimaActivData;
use src\asistentes\application\ListaUltimQueCtrData;
use src\asistentes\application\QueCtrListaData;
use src\asistentes\application\TablaPeticionesData;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;
use src\asistentes\application\AsistenteMutationCtx;
use src\shared\security\HashB;

/**
 * Contrato de los controladores HTTP de `asistentes`: pasan `$_POST` al caso de uso y responden con el envelope de
 * `ContestarJson` (`success`, `mensaje`, `data` como cadena JSON). Se ejecutan en proceso, como los llama
 * `PostRequest`, con el caso de uso sustituido por un doble.
 */
final class AsistentesControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/asistentes/infrastructure/ui/http/controllers/';

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    /**
     * Ejecuta el controlador con `$_POST` y devuelve el JSON que emite.
     *
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function ejecutar(string $controlador, array $post): array
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

    /** Doble del caso de uso: guarda la entrada y devuelve lo indicado, con el método que llame el controlador. */
    private function doble(mixed $resultado): object
    {
        return new class($resultado) {
            /** @var array<string, mixed>|null */
            public ?array $entrada = null;

            public function __construct(private mixed $resultado)
            {
            }

            /** @param array<string, mixed> $input */
            public function build(array $input): mixed
            {
                $this->entrada = $input;

                return $this->resultado;
            }

            /** @var array<string, mixed>|null */
            public ?array $fijado = null;

            /**
             * @param array<string, mixed> $input
             * @param array<string, mixed> $fijado
             */
            public function execute(array $input, array $fijado = []): mixed
            {
                $this->entrada = $input;
                $this->fijado = $fijado;

                return $this->resultado;
            }
        };
    }

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function lecturasProvider(): iterable
    {
        yield 'activ_pendientes_select_data' => ['activ_pendientes_select_data.php', ActivPendientesSelectData::class];
        yield 'asistente_mover_data' => ['asistente_mover_data.php', AsistenteMoverData::class];
        yield 'form_actividades_de_una_persona_data' => ['form_actividades_de_una_persona_data.php', FormActividadesDeUnaPersonaData::class];
        yield 'form_asistentes_a_una_actividad_data' => ['form_asistentes_a_una_actividad_data.php', FormAsistentesAUnaActividadData::class];
        yield 'lista_activ_ctr_data' => ['lista_activ_ctr_data.php', ListaActivCtrData::class];
        yield 'lista_asis_conjunto_activ_data' => ['lista_asis_conjunto_activ_data.php', ListaAsisConjuntoActivData::class];
        yield 'lista_asistentes_data' => ['lista_asistentes_data.php', ListaAsistentesData::class];
        yield 'lista_est_ctr_data' => ['lista_est_ctr_data.php', ListaEstCtrData::class];
        yield 'lista_ultima_activ_data' => ['lista_ultima_activ_data.php', ListaUltimaActivData::class];
        yield 'lista_ultim_que_ctr_data' => ['lista_ultim_que_ctr_data.php', ListaUltimQueCtrData::class];
        yield 'que_ctr_lista_data' => ['que_ctr_lista_data.php', QueCtrListaData::class];
        yield 'tabla_peticiones_data' => ['tabla_peticiones_data.php', TablaPeticionesData::class];
    }

    #[DataProvider('lecturasProvider')]
    public function test_las_lecturas_devuelven_los_datos_del_caso_de_uso(string $controlador, string $caso): void
    {
        $doble = $this->doble(['clave' => 'valor', 'lista' => [1, 2]]);
        $this->sustituirServicio($caso, $doble);

        $json = $this->ejecutar($controlador, ['id_pau' => '3001', 'que' => 'x']);

        $this->assertSame(['id_pau' => '3001', 'que' => 'x'], $doble->entrada);
        $this->assertTrue($json['success']);
        $this->assertArrayNotHasKey('mensaje', $json);
        $this->assertSame(['clave' => 'valor', 'lista' => [1, 2]], json_decode($json['data'], true));
    }

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function conErrorProvider(): iterable
    {
        yield 'form_actividades_de_una_persona_data' => ['form_actividades_de_una_persona_data.php', FormActividadesDeUnaPersonaData::class];
        yield 'form_asistentes_a_una_actividad_data' => ['form_asistentes_a_una_actividad_data.php', FormAsistentesAUnaActividadData::class];
        yield 'tabla_peticiones_data' => ['tabla_peticiones_data.php', TablaPeticionesData::class];
    }

    #[DataProvider('conErrorProvider')]
    public function test_las_lecturas_con_error_responden_success_false_con_el_mensaje(string $controlador, string $caso): void
    {
        $this->sustituirServicio($caso, $this->doble(['error' => 'no encuentro a nadie']));

        $json = $this->ejecutar($controlador, []);

        $this->assertFalse($json['success']);
        $this->assertSame('no encuentro a nadie', $json['mensaje']);
        $this->assertSame('none', $json['data']);
    }

    public function test_un_aviso_previsto_no_es_un_error(): void
    {
        $this->sustituirServicio(FormAsistentesAUnaActividadData::class, $this->doble(['aviso_txt' => 'lo modifica el propietario']));

        $json = $this->ejecutar('form_asistentes_a_una_actividad_data.php', []);

        $this->assertTrue($json['success']);
        $this->assertSame(['aviso_txt' => 'lo modifica el propietario'], json_decode($json['data'], true));
    }

    /**
     * Controlador, caso de uso y campo de su cápsula. La cápsula se firma dentro del test
     * ({@see capsulaDe}): va ligada a la sesión, y la del momento en que se evalúa el proveedor no vale después.
     *
     * @return iterable<string, array{string, class-string, string}>
     */
    public static function mutacionesProvider(): iterable
    {
        yield 'asistente_guardar' => ['asistente_guardar.php', AsistenteGuardar::class, 'ctx_guardar'];
        yield 'asistente_eliminar' => ['asistente_eliminar.php', AsistenteEliminar::class, 'ctx_eliminar_asistente'];
        yield 'asistente_plaza_asignar' => ['asistente_plaza_asignar.php', AsistentePlazaAsignar::class, 'ctx_plaza'];
    }

    private static function capsulaDe(string $campo): string
    {
        return match ($campo) {
            'ctx_guardar' => AsistenteMutationCtx::guardar(3001, 10011),
            'ctx_eliminar_asistente' => AsistenteMutationCtx::eliminar(3001, 10011),
            default => AsistenteMutationCtx::plazaAsignar(3001),
        };
    }

    #[DataProvider('mutacionesProvider')]
    public function test_las_mutaciones_responden_ok_o_el_mensaje_de_error(string $controlador, string $caso, string $campo): void
    {
        $capsula = self::capsulaDe($campo);
        $this->sustituirServicio($caso, $this->doble(''));
        $ok = $this->ejecutar($controlador, [$campo => $capsula]);
        $this->assertSame(['success' => true, 'data' => 'ok'], $ok);

        $this->sustituirServicio($caso, $this->doble('los datos los modifica otra dl'));
        $error = $this->ejecutar($controlador, [$campo => $capsula]);
        $this->assertSame(['success' => false, 'mensaje' => 'los datos los modifica otra dl', 'data' => 'ok'], $error);
    }

    /** Sin la cápsula de su pantalla (o con la de otra acción) no se llama al caso de uso (fase 6.5). */
    #[DataProvider('mutacionesProvider')]
    public function test_las_mutaciones_exigen_la_capsula_de_su_pantalla(string $controlador, string $caso, string $campo): void
    {
        $doble = $this->doble('');
        $this->sustituirServicio($caso, $doble);
        foreach (['sin cápsula' => [], 'falsa' => [$campo => 'x'], 'de otra acción' => [$campo => HashB::sign('otra_accion', ['id_activ' => 3001, 'id_nom' => 10011])]] as $txt => $post) {
            $json = $this->ejecutar($controlador, $post + ['id_activ' => '3001', 'id_nom' => '10011']);
            $this->assertFalse($json['success'], $txt);
            $this->assertSame(_("Operación no autorizada"), $json['mensaje'], $txt);
        }
        $this->assertNull($doble->entrada, 'el caso de uso no se ha llamado');
    }

    public function test_guardar_pasa_al_caso_de_uso_lo_que_fija_la_capsula(): void
    {
        $doble = $this->doble('');
        $this->sustituirServicio(AsistenteGuardar::class, $doble);

        $this->ejecutar('asistente_guardar.php', ['ctx_guardar' => AsistenteMutationCtx::guardar(0, 10011, 3001), 'id_activ' => '3002', 'mod' => 'mover']);

        $this->assertSame(['id_activ' => 0, 'id_nom' => 10011, 'id_activ_old' => 3001], $doble->fijado);
        $this->assertSame('3002', $doble->entrada['id_activ'] ?? null, 'lo que no fija la cápsula llega del formulario');
    }

    public function test_una_capsula_de_guardar_que_no_fija_ni_actividad_ni_persona_no_vale(): void
    {
        $doble = $this->doble('');
        $this->sustituirServicio(AsistenteGuardar::class, $doble);

        $json = $this->ejecutar('asistente_guardar.php', ['ctx_guardar' => AsistenteMutationCtx::guardar(0, 0), 'id_activ' => '3001', 'id_nom' => '10011', 'mod' => 'nuevo']);

        $this->assertFalse($json['success']);
        $this->assertNull($doble->entrada);
    }

    /** La asistencia que se borra y la actividad de la plaza salen de la cápsula, no del POST. */
    public function test_eliminar_y_asignar_plaza_actuan_sobre_lo_que_fija_la_capsula(): void
    {
        $doble = $this->doble('');
        $this->sustituirServicio(AsistenteEliminar::class, $doble);
        $this->ejecutar('asistente_eliminar.php', ['ctx_eliminar_asistente' => AsistenteMutationCtx::eliminar(3001, 10011), 'pau' => 'a', 'id_pau' => '9999', 'sel' => ['8888#0#1#1']]);
        $this->assertSame(['pau' => 'p', 'sel' => [], 'id_activ' => 3001, 'id_nom' => 10011], $doble->entrada);

        $doble = $this->doble('');
        $this->sustituirServicio(AsistentePlazaAsignar::class, $doble);
        $this->ejecutar('asistente_plaza_asignar.php', ['ctx_plaza' => AsistenteMutationCtx::plazaAsignar(3001), 'id_activ' => '9999', 'plaza' => '4', 'lista_json' => '[]']);
        $this->assertSame(3001, $doble->entrada['id_activ'] ?? null);
        $this->assertSame('4', $doble->entrada['plaza'] ?? null);
    }

    public function test_cada_ruta_apunta_a_un_controlador_y_cada_controlador_tiene_ruta(): void
    {
        $rutas = file_get_contents(self::DIR . '../../../../config/routes.php') ?: '';
        preg_match_all("#'/src/asistentes/([a-z_]+)'#", $rutas, $m);
        $declaradas = $m[1];
        sort($declaradas);
        $ficheros = array_map(static fn (string $f): string => basename($f, '.php'), glob(self::DIR . '*.php') ?: []);
        sort($ficheros);

        $this->assertSame($ficheros, $declaradas);
        $this->assertSame(count($declaradas), count(array_unique($declaradas)), 'sin rutas repetidas');
    }
}
