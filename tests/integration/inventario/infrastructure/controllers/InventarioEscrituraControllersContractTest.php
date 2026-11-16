<?php

declare(strict_types=1);

namespace Tests\integration\inventario\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\inventario\application\DocumentosAsignar;
use src\inventario\application\DocumentosTrasladar;
use src\inventario\application\EquipajeEliminar;
use src\inventario\application\EquipajeMaletas;
use src\inventario\application\EquipajeNuevo;
use src\inventario\application\EquipajeTextoGuardar;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los controladores que escriben en `inventario`: solo actúan con la cápsula que firma su pantalla,
 * lo que fija la cápsula (equipaje, maleta, tipo de documento, documentos a trasladar) sale de ella y no del POST,
 * y lo que devuelve el caso de uso llega en el envelope de `ContestarJson`. En proceso, como los llama
 * `PostRequest`, con el caso de uso sustituido por un doble.
 */
final class InventarioEscrituraControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/inventario/infrastructure/ui/http/controllers/';

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
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function ejecutar(string $controlador, array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::DIR . $controlador . '.php';
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));

        return $json;
    }

    /** Doble del caso de uso: anota el método llamado y sus argumentos y devuelve lo indicado. */
    private function doble(mixed $resultado): object
    {
        return new class($resultado) {
            public ?string $metodo = null;
            /** @var list<mixed> */
            public array $argumentos = [];

            public function __construct(private mixed $resultado)
            {
            }

            /** @param list<mixed> $argumentos */
            public function __call(string $metodo, array $argumentos): mixed
            {
                $this->metodo = $metodo;
                $this->argumentos = $argumentos;

                return $this->resultado;
            }
        };
    }

    /**
     * Controlador, campo de la cápsula, payload firmado, POST, caso de uso, método y argumentos esperados.
     *
     * @return iterable<string, array{string, string, array<string, mixed>, array<string, mixed>, class-string, string, list<mixed>}>
     */
    public static function escriturasProvider(): iterable
    {
        $seleccion = rawurlencode((string) json_encode([11, '12', 'x']));
        $fechas = ['f_recibido' => '1/1/2027', 'f_asignado' => '2/1/2027'];

        yield 'equipajes_nuevo_guardar' => [
            'equipajes_nuevo_guardar', 'ctx_nuevo', ['id_ubi_activ' => 30],
            ['id_ubi_activ' => '99', 'nom_equipaje' => 'Equipaje', 'ids_activ' => '1,2', 'f_ini' => '1/1/2027', 'f_fin' => '2/1/2027', 'lugar' => 'casa'],
            EquipajeNuevo::class, 'execute', [30, 'Equipaje', '1,2', '1/1/2027', '2/1/2027', 'casa'],
        ];
        yield 'doc_asignar_ctr_guardar' => [
            'doc_asignar_ctr_guardar', 'ctx_guardar', ['id_tipo_doc' => 7],
            ['id_tipo_doc' => '8', 'str_selected_id' => $seleccion, 'num_11' => '3', 'num_12' => '4'] + $fechas,
            DocumentosAsignar::class, 'aCentros', [7, [11 => '3', 12 => '4'], '1/1/2027', '2/1/2027'],
        ];
        yield 'doc_asignar_dlb_guardar' => [
            'doc_asignar_dlb_guardar', 'ctx_guardar', ['id_tipo_doc' => 7],
            ['id_tipo_doc' => '8', 'str_selected_id' => $seleccion, 'num_11' => '3'] + $fechas,
            DocumentosAsignar::class, 'aLugares', [7, [11 => '3', 12 => 0], '1/1/2027', '2/1/2027'],
        ];
        yield 'equipajes_eliminar' => [
            'equipajes_eliminar', 'ctx_eliminar', ['id_equipaje' => 40],
            ['id_equipaje' => '41'],
            EquipajeEliminar::class, 'execute', [40],
        ];
        yield 'equipajes_eliminar_grupo' => [
            'equipajes_eliminar_grupo', 'ctx_eliminar_grupo', ['id_equipaje' => 40, 'id_grupo' => 2],
            ['id_equipaje' => '41', 'id_grupo' => '3'],
            EquipajeMaletas::class, 'eliminarGrupo', [40, 2],
        ];
        yield 'equipajes_add_doc' => [
            'equipajes_add_doc', 'ctx_add', ['id_item_egm' => 77],
            ['id_item_egm' => '78', 'sel' => ['5', '6']],
            EquipajeMaletas::class, 'anyadirDocumentos', [77, ['5', '6']],
        ];
        yield 'equipajes_del_doc' => [
            'equipajes_del_doc', 'ctx_del', ['id_item_egm' => 77],
            ['id_item_egm' => '78', 'sel' => ['5']],
            EquipajeMaletas::class, 'quitarDocumentos', [77, ['5']],
        ];
        yield 'equipajes_texto_listado_guardar' => [
            'equipajes_texto_listado_guardar', 'ctx_guardar', ['id_equipaje' => 40],
            ['id_equipaje' => '41', 'loc' => 'pie', 'texto' => 'Texto del pie'],
            EquipajeTextoGuardar::class, 'execute', [40, 'pie', 'Texto del pie'],
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $post
     * @param list<mixed> $argumentos
     */
    #[DataProvider('escriturasProvider')]
    public function test_con_su_cápsula_llama_al_caso_de_uso_con_lo_que_fija_la_cápsula(
        string $controlador,
        string $campo,
        array $payload,
        array $post,
        string $caso,
        string $metodo,
        array $argumentos,
    ): void {
        $doble = $this->doble('');
        $this->sustituirServicio($caso, $doble);

        $json = $this->ejecutar($controlador, [$campo => HashB::sign($controlador, $payload)] + $post);

        $this->assertTrue($json['success'], (string) ($json['mensaje'] ?? ''));
        $this->assertSame($metodo, $doble->metodo);
        $this->assertSame($argumentos, $doble->argumentos);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $post
     */
    #[DataProvider('escriturasProvider')]
    public function test_con_una_cápsula_falsa_o_de_otra_acción_no_llama_al_caso_de_uso(
        string $controlador,
        string $campo,
        array $payload,
        array $post,
        string $caso,
    ): void {
        foreach (['falsa' => 'xxx', 'otra acción' => HashB::sign('otra_accion', $payload), 'ninguna' => null] as $tipo => $ctx) {
            $doble = $this->doble('');
            $this->sustituirServicio($caso, $doble);
            $peticion = $ctx === null ? $post : [$campo => $ctx] + $post;

            $json = $this->ejecutar($controlador, $peticion);

            $this->assertFalse($json['success'], $tipo);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $tipo);
            $this->assertNull($doble->metodo, $tipo);
        }
    }

    public function test_un_error_del_caso_de_uso_llega_como_success_false(): void
    {
        $this->sustituirServicio(EquipajeEliminar::class, $this->doble('hay un error, no se ha eliminado'));

        $json = $this->ejecutar('equipajes_eliminar', ['ctx_eliminar' => HashB::sign('equipajes_eliminar', ['id_equipaje' => 40])]);

        $this->assertFalse($json['success']);
        $this->assertSame('hay un error, no se ha eliminado', $json['mensaje']);
    }

    public function test_actualizar_una_maleta_devuelve_su_id_y_toma_equipaje_grupo_y_lugar_de_la_cápsula(): void
    {
        $doble = $this->doble(['error' => '', 'id_item_egm' => 77]);
        $this->sustituirServicio(EquipajeMaletas::class, $doble);
        $ctx = HashB::sign('equipajes_update_grupo', ['id_equipaje' => 40, 'id_grupo' => 2, 'id_lugar' => 50]);

        $json = $this->ejecutar('equipajes_update_grupo', ['ctx_update_grupo' => $ctx, 'id_equipaje' => '41', 'sel' => ['5', '6']]);

        $this->assertTrue($json['success']);
        $this->assertSame(['id_item_egm' => 77], json_decode((string) $json['data'], true));
        $this->assertSame('guardarGrupo', $doble->metodo);
        $this->assertSame([40, 2, 50, ['5', '6']], $doble->argumentos);
    }

    public function test_actualizar_una_maleta_sin_cápsula_no_llama_al_caso_de_uso(): void
    {
        $doble = $this->doble(['error' => '', 'id_item_egm' => 77]);
        $this->sustituirServicio(EquipajeMaletas::class, $doble);

        $json = $this->ejecutar('equipajes_update_grupo', ['ctx_update_grupo' => HashB::sign('equipajes_add_doc', ['id_item_egm' => 1])]);

        $this->assertFalse($json['success']);
        $this->assertNull($doble->metodo);
    }

    public function test_trasladar_toma_los_documentos_de_las_cápsulas_y_descarta_las_no_válidas(): void
    {
        $doble = $this->doble('');
        $this->sustituirServicio(DocumentosTrasladar::class, $doble);

        $json = $this->ejecutar('traslado_doc_guardar', [
            'sel' => [
                HashB::sign('traslado_doc_guardar', ['id_doc' => 5]),
                'xxx',
                HashB::sign('equipajes_add_doc', ['id_doc' => 9]),
                HashB::sign('traslado_doc_guardar', ['id_doc' => 6]),
            ],
            'id_ubi_new' => '10',
            'id_lugar_new' => '20',
        ]);

        $this->assertTrue($json['success']);
        $this->assertSame([[5, 6], 10, 20], $doble->argumentos);
    }

    public function test_trasladar_sin_ninguna_cápsula_válida_no_llama_al_caso_de_uso(): void
    {
        foreach (['vacío' => [], 'solo no válidas' => ['xxx', HashB::sign('equipajes_add_doc', ['id_doc' => 9])]] as $caso => $sel) {
            $doble = $this->doble('');
            $this->sustituirServicio(DocumentosTrasladar::class, $doble);

            $json = $this->ejecutar('traslado_doc_guardar', ['sel' => $sel, 'id_ubi_new' => '10']);

            $this->assertFalse($json['success'], $caso);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $caso);
            $this->assertNull($doble->metodo, $caso);
        }
    }

    public function test_cabecera_y_pie_sin_su_cápsula_no_escriben_el_fichero(): void
    {
        $fichero = \src\shared\config\ConfigGlobal::$dir_web . '/data/inventario/cabecera_pie_textos.ini';
        $antes = is_file($fichero) ? md5_file($fichero) : null;

        foreach (['ninguna' => [], 'falsa' => ['ctx_guardar' => 'xxx'], 'otra acción' => ['ctx_guardar' => HashB::sign('otra_accion', [])]] as $caso => $ctx) {
            $json = $this->ejecutar('cabecera_pie_txt_guardar', $ctx + ['cabecera' => 'NO', 'pie' => 'NO']);

            $this->assertFalse($json['success'], $caso);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $caso);
        }
        $this->assertSame($antes, is_file($fichero) ? md5_file($fichero) : null);
    }
}
