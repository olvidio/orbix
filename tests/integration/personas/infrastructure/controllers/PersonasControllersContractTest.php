<?php

declare(strict_types=1);

namespace Tests\integration\personas\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\personas\application\HomePersonaData;
use src\personas\application\PersonaEliminar;
use src\personas\application\PersonaPublicar;
use src\personas\application\PersonaPublicarFormData;
use src\personas\application\PersonasEditarData;
use src\personas\application\PersonasSelectData;
use src\personas\application\PersonaUpdate;
use src\personas\application\StgrCambioData;
use src\personas\application\StgrUpdate;
use src\personas\application\TrasladoFormData;
use src\personas\application\TrasladoUpdate;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los controladores HTTP de `personas`: token de las mutaciones,
 * que la persona sale del token y no del formulario, envelope de `ContestarJson`,
 * avisos «suaves» (dl sin región stgr, persona no válida) como respuesta válida,
 * y que ninguno corte la petición (en proceso cortaría al frontend que llama).
 *
 * Los casos de uso se sustituyen en el contenedor por dobles que apuntan las llamadas.
 */
final class PersonasControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/personas/infrastructure/ui/http/controllers/';

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

    // ------------------------------------------------------------ mutaciones

    /** @return iterable<string, array{string, class-string, string, string}> */
    public static function mutacionesProvider(): iterable
    {
        yield 'eliminar' => ['persona_eliminar.php', PersonaEliminar::class, 'ctx_eliminar', 'persona_eliminar'];
        yield 'publicar' => ['persona_publicar.php', PersonaPublicar::class, 'ctx_publicar', 'persona_publicar'];
        yield 'update' => ['persona_update.php', PersonaUpdate::class, 'ctx_update', 'persona_update'];
        yield 'stgr' => ['stgr_update.php', StgrUpdate::class, 'ctx_update', 'stgr_update'];
        yield 'traslado' => ['traslado_update.php', TrasladoUpdate::class, 'ctx_update', 'traslado_update'];
    }

    #[DataProvider('mutacionesProvider')]
    public function test_mutacion_sin_token_valido_no_llama_al_caso_de_uso(string $ctrl, string $clase, string $campo, string $accion): void
    {
        $caso = $this->doble($clase, ['execute' => '']);
        foreach (['sin token' => [], 'token falso' => [$campo => 'x'], 'token de otra acción' => [$campo => HashB::sign('otra_accion', ['id_nom' => 1])]] as $txt => $post) {
            $json = $this->ejecutar($ctrl, $post);
            $this->assertFalse($json['success'], $txt);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $txt);
        }
        $this->assertSame([], $caso->llamadas);
    }

    #[DataProvider('mutacionesProvider')]
    public function test_mutacion_con_token_responde_ok_o_el_error_del_caso_de_uso(string $ctrl, string $clase, string $campo, string $accion): void
    {
        $token = HashB::sign($accion, ['id_nom' => 100555, 'obj_pau' => 'PersonaN', 'id_schema' => 1001, 'id_tabla' => 'n', 'id_pau' => 100555]);
        $caso = $this->doble($clase, ['execute' => '']);
        $json = $this->ejecutar($ctrl, [$campo => $token]);
        $this->assertTrue($json['success']);
        $this->assertSame('ok', $json['data']);
        $this->assertCount(1, $caso->llamadas);

        $this->doble($clase, ['execute' => 'no se ha guardado']);
        $json = $this->ejecutar($ctrl, [$campo => $token]);
        $this->assertFalse($json['success']);
        $this->assertSame('no se ha guardado', $json['mensaje']);
    }

    public function test_la_persona_sale_del_token_y_no_del_formulario(): void
    {
        $post = ['id_nom' => '666', 'obj_pau' => 'PersonaAgd', 'id_pau' => '666', 'id_tabla' => 'a', 'id_schema' => '9'];

        $caso = $this->doble(PersonaEliminar::class, ['execute' => '']);
        $this->ejecutar('persona_eliminar.php', $post + ['ctx_eliminar' => HashB::sign('persona_eliminar', ['id_nom' => 100555, 'obj_pau' => 'PersonaN'])]);
        $this->assertSame([100555, 'PersonaN'], $caso->args('execute'));

        $caso = $this->doble(StgrUpdate::class, ['execute' => '']);
        $this->ejecutar('stgr_update.php', $post + ['nivel_stgr' => '4', 'ctx_update' => HashB::sign('stgr_update', ['id_nom' => 100555, 'id_tabla' => 'n'])]);
        $this->assertSame([100555, 'n', '4'], $caso->args('execute'));

        $caso = $this->doble(PersonaUpdate::class, ['execute' => '']);
        $this->ejecutar('persona_update.php', $post + ['apellido1' => 'García', 'ctx_update' => HashB::sign('persona_update', ['id_nom' => 100555, 'obj_pau' => 'PersonaN'])]);
        $input = $caso->args('execute')[0];
        $this->assertSame([100555, 'PersonaN', 'García'], [$input['id_nom'], $input['obj_pau'], $input['apellido1']]);

        $caso = $this->doble(TrasladoUpdate::class, ['execute' => '']);
        $this->ejecutar('traslado_update.php', $post + ['new_dl' => 'dlp', 'ctx_update' => HashB::sign('traslado_update', ['id_pau' => 100555, 'obj_pau' => 'PersonaN'])]);
        $input = $caso->args('execute')[0];
        $this->assertSame([100555, 'PersonaN', 'dlp'], [$input['id_pau'], $input['obj_pau'], $input['new_dl']]);
    }

    public function test_publicar_toma_la_persona_del_token_y_las_dl_del_formulario(): void
    {
        $token = HashB::sign('persona_publicar', ['id_nom' => 100555, 'id_schema' => 1001]);
        foreach ([[['dlp', 'dlz'], ['dlp', 'dlz']], ['dlp', ['dlp']], ['', []]] as [$dl, $esperado]) {
            $caso = $this->doble(PersonaPublicar::class, ['execute' => '']);
            $this->ejecutar('persona_publicar.php', ['ctx_publicar' => $token, 'dl' => $dl, 'id_nom' => '666']);
            $this->assertSame([100555, 1001, $esperado], $caso->args('execute'));
        }
    }

    // ------------------------------------------------------------ lecturas

    /** @return iterable<string, array{string, class-string}> */
    public static function lecturasProvider(): iterable
    {
        yield 'home' => ['home_persona_data.php', HomePersonaData::class];
        yield 'editar' => ['personas_editar_data.php', PersonasEditarData::class];
        yield 'select' => ['personas_select_data.php', PersonasSelectData::class];
        yield 'publicar form' => ['persona_publicar_form_data.php', PersonaPublicarFormData::class];
        yield 'stgr form' => ['stgr_cambio_data.php', StgrCambioData::class];
        yield 'traslado form' => ['traslado_form_data.php', TrasladoFormData::class];
    }

    #[DataProvider('lecturasProvider')]
    public function test_lectura_pasa_el_post_y_devuelve_los_datos(string $ctrl, string $clase): void
    {
        $caso = $this->doble($clase, ['execute' => ['id_nom' => 100555, 'titulo' => 'García']]);
        $json = $this->ejecutar($ctrl, ['id_nom' => '100555', 'obj_pau' => 'PersonaN']);
        $this->assertTrue($json['success']);
        $this->assertSame(['id_nom' => 100555, 'titulo' => 'García'], self::data($json));
        $this->assertSame(['id_nom' => '100555', 'obj_pau' => 'PersonaN'], $caso->args('execute')[0]);
    }

    #[DataProvider('lecturasProvider')]
    public function test_lectura_con_error_responde_error(string $ctrl, string $clase): void
    {
        $this->doble($clase, ['execute' => ['error' => 'No existe la clase de la persona']]);
        $json = $this->ejecutar($ctrl, []);
        $this->assertFalse($json['success']);
        $this->assertSame('No existe la clase de la persona', $json['mensaje']);
    }

    public function test_home_y_select_convierten_persona_no_valida_en_aviso(): void
    {
        foreach (['home_persona_data.php' => HomePersonaData::class, 'personas_select_data.php' => PersonasSelectData::class] as $ctrl => $clase) {
            $this->doble($clase, ['execute' => ['error' => 'persona no válida', 'id_nom' => 1]]);
            $json = $this->ejecutar($ctrl, []);
            $this->assertTrue($json['success'], $ctrl);
            $data = self::data($json);
            $this->assertArrayNotHasKey('error', $data, $ctrl);
            $this->assertStringContainsString('persona no válida', $data['aviso'], $ctrl);
        }
    }

    public function test_select_con_dl_sin_region_stgr_devuelve_lista_vacia_con_aviso(): void
    {
        $this->doble(PersonasSelectData::class, ['execute' => new \RuntimeException('falta indicar a que región del stgr pertenece la dl: dlz')]);
        $json = $this->ejecutar('personas_select_data.php', ['tabla' => 'p_de_paso_ex']);
        $this->assertTrue($json['success']);
        $data = self::data($json);
        $this->assertSame(['p_de_paso_ex', [], 0], [$data['tabla'], $data['personas'], $data['total']]);
        $this->assertNotSame('', $data['aviso']);
    }

    public function test_select_otra_excepcion_responde_error(): void
    {
        $this->doble(PersonasSelectData::class, ['execute' => new \RuntimeException('SQLSTATE fallo')]);
        $json = $this->ejecutar('personas_select_data.php', ['tabla' => 'p_numerarios']);
        $this->assertFalse($json['success']);
        $this->assertSame('SQLSTATE fallo', $json['mensaje']);
    }
}
