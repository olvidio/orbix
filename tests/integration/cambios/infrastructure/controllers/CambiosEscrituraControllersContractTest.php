<?php

declare(strict_types=1);

namespace Tests\integration\cambios\infrastructure\controllers;

use src\cambios\application\CambioUsuarioEliminar;
use src\cambios\application\CambioUsuarioEliminarHastaFecha;
use src\cambios\application\CambioUsuarioObjetoPrefEliminar;
use src\cambios\application\CambioUsuarioObjetoPrefGuardar;
use src\cambios\application\CambioUsuarioPropiedadPrefGuardarTodas;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los controladores que escriben en `cambios` (preferencias de avisos y avisos de un usuario): solo
 * actúan con la cápsula que firma su pantalla y el objeto, el usuario o el aviso salen de la cápsula, no del POST.
 * En proceso, con el caso de uso sustituido por un doble.
 */
final class CambiosEscrituraControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/cambios/infrastructure/ui/http/controllers/';

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

    /** Doble de `execute(array $input)`: anota la entrada y devuelve lo indicado. */
    private function doble(array $resultado): object
    {
        return new class($resultado) {
            /** @var array<string, mixed>|null */
            public ?array $entrada = null;

            /** @param array<string, mixed> $resultado */
            public function __construct(private array $resultado)
            {
            }

            /**
             * @param array<string, mixed> $input
             * @return array<string, mixed>
             */
            public function execute(array $input): array
            {
                $this->entrada = $input;

                return $this->resultado;
            }
        };
    }

    public function test_guardar_objeto_toma_preferencia_y_usuario_de_la_cápsula_y_firma_la_de_propiedades(): void
    {
        $doble = $this->doble(['error' => '', 'id_item_usuario_objeto' => 321]);
        $this->sustituirServicio(CambioUsuarioObjetoPrefGuardar::class, $doble);
        $ctx = HashB::sign('cambio_usuario_objeto_pref_guardar', ['id_item_usuario_objeto' => 300, 'id_usuario' => 40]);

        $json = $this->ejecutar('cambio_usuario_objeto_pref_guardar', [
            'ctx_guardar' => $ctx,
            'id_item_usuario_objeto' => '999',
            'id_usuario' => '1',
            'id_tipo_activ' => '1.....',
            'dl_propia' => 't',
            'objeto' => 'Actividad',
            'aviso_tipo' => '2',
            'id_fase_ref' => '5',
            'casas' => ['10', '11'],
        ]);

        $this->assertTrue($json['success'], (string) ($json['mensaje'] ?? ''));
        $this->assertSame([300, 40, 'Actividad', 2, 5, ['10', '11']], [
            $doble->entrada['id_item_usuario_objeto'], $doble->entrada['id_usuario'], $doble->entrada['objeto'],
            $doble->entrada['aviso_tipo'], $doble->entrada['id_fase_ref'], $doble->entrada['casas'],
        ]);
        $data = json_decode((string) $json['data'], true);
        $this->assertSame(321, $data['id_item_usuario_objeto']);
        $this->assertSame(['id_item_usuario_objeto' => 321], HashB::open($data['ctx_guardar_propiedades'], 'cambio_usuario_propiedad_pref_guardar_todas'));
    }

    public function test_guardar_objeto_con_error_no_firma_la_cápsula_de_propiedades(): void
    {
        $this->sustituirServicio(CambioUsuarioObjetoPrefGuardar::class, $this->doble(['error' => 'falta el tipo de aviso', 'id_item_usuario_objeto' => 321]));

        $json = $this->ejecutar('cambio_usuario_objeto_pref_guardar', [
            'ctx_guardar' => HashB::sign('cambio_usuario_objeto_pref_guardar', ['id_item_usuario_objeto' => 300, 'id_usuario' => 40]),
        ]);

        $this->assertFalse($json['success']);
        $this->assertSame('falta el tipo de aviso', $json['mensaje']);
        $this->assertArrayNotHasKey('ctx_guardar_propiedades', json_decode((string) $json['data'], true));
    }

    public function test_guardar_propiedades_toma_el_objeto_de_la_cápsula(): void
    {
        $doble = $this->doble(['error' => '']);
        $this->sustituirServicio(CambioUsuarioPropiedadPrefGuardarTodas::class, $doble);

        $json = $this->ejecutar('cambio_usuario_propiedad_pref_guardar_todas', [
            'ctx_guardar_propiedades' => HashB::sign('cambio_usuario_propiedad_pref_guardar_todas', ['id_item_usuario_objeto' => 321]),
            'id_item_usuario_objeto_prop' => '999',
            'objeto_prop' => 'Actividad',
            'Actividad' => ['Actividad_nom_activ_cond'],
        ]);

        $this->assertTrue($json['success']);
        $this->assertSame(321, $doble->entrada['id_item_usuario_objeto_prop']);
        $this->assertSame('Actividad', $doble->entrada['objeto_prop']);
    }

    public function test_eliminar_objeto_toma_la_preferencia_de_la_cápsula(): void
    {
        $doble = $this->doble(['error' => '']);
        $this->sustituirServicio(CambioUsuarioObjetoPrefEliminar::class, $doble);

        $json = $this->ejecutar('cambio_usuario_objeto_pref_eliminar', [
            'ctx_eliminar' => HashB::sign('cambio_usuario_objeto_pref_eliminar', ['id_item_usuario_objeto' => 321]),
            'id_item_usuario_objeto' => '999',
        ]);

        $this->assertTrue($json['success']);
        $this->assertSame(['id_item_usuario_objeto' => 321], $doble->entrada);
    }

    public function test_eliminar_hasta_fecha_pasa_la_fecha(): void
    {
        $doble = $this->doble(['ok' => true, 'mensaje' => '']);
        $this->sustituirServicio(CambioUsuarioEliminarHastaFecha::class, $doble);

        $json = $this->ejecutar('cambio_usuario_eliminar_hasta_fecha', [
            'ctx_eliminar_fecha' => HashB::sign('cambio_usuario_eliminar_hasta_fecha'),
            'f_fin' => '1/11/2026',
        ]);

        $this->assertTrue($json['success']);
        $this->assertSame(['f_fin' => '1/11/2026'], $doble->entrada);
    }

    public function test_eliminar_hasta_fecha_con_error_llega_como_success_false(): void
    {
        $this->sustituirServicio(CambioUsuarioEliminarHastaFecha::class, $this->doble(['ok' => false, 'mensaje' => 'fecha no válida']));

        $json = $this->ejecutar('cambio_usuario_eliminar_hasta_fecha', [
            'ctx_eliminar_fecha' => HashB::sign('cambio_usuario_eliminar_hasta_fecha'),
            'f_fin' => 'x',
        ]);

        $this->assertSame([false, 'fecha no válida'], [$json['success'], $json['mensaje']]);
    }

    public function test_sin_su_cápsula_no_llaman_al_caso_de_uso(): void
    {
        $casos = [
            'cambio_usuario_objeto_pref_guardar' => ['ctx_guardar', CambioUsuarioObjetoPrefGuardar::class, ['error' => '']],
            'cambio_usuario_propiedad_pref_guardar_todas' => ['ctx_guardar_propiedades', CambioUsuarioPropiedadPrefGuardarTodas::class, ['error' => '']],
            'cambio_usuario_objeto_pref_eliminar' => ['ctx_eliminar', CambioUsuarioObjetoPrefEliminar::class, ['error' => '']],
            'cambio_usuario_eliminar_hasta_fecha' => ['ctx_eliminar_fecha', CambioUsuarioEliminarHastaFecha::class, ['ok' => true, 'mensaje' => '']],
        ];
        foreach ($casos as $controlador => [$campo, $caso, $resultado]) {
            foreach (['falsa' => 'xxx', 'otra acción' => HashB::sign('otra_accion', ['id_item_usuario_objeto' => 321]), 'ninguna' => null] as $tipo => $ctx) {
                $doble = $this->doble($resultado);
                $this->sustituirServicio($caso, $doble);

                $json = $this->ejecutar($controlador, $ctx === null ? [] : [$campo => $ctx]);

                $this->assertSame([false, 'Operación no autorizada'], [$json['success'], $json['mensaje']], "$controlador ($tipo)");
                $this->assertNull($doble->entrada, "$controlador ($tipo)");
            }
            $this->reponerServicios();
        }
    }

    public function test_eliminar_avisos_toma_cada_aviso_de_su_cápsula_y_descarta_las_no_válidas(): void
    {
        $doble = $this->doble(['ok' => true, 'mensaje' => '']);
        $this->sustituirServicio(CambioUsuarioEliminar::class, $doble);
        $fila = static fn(int $item) => HashB::sign('cambio_usuario_eliminar', ['id_item_cambio' => $item, 'id_usuario' => 40, 'sfsv' => 1, 'aviso_tipo' => 2]);

        $json = $this->ejecutar('cambio_usuario_eliminar', ['sel' => [$fila(7), 'xxx', '7#1#1#1', HashB::sign('otra_accion', ['id_item_cambio' => 9]), $fila(8)]]);

        $this->assertTrue($json['success']);
        $this->assertSame(['sel' => ['7#40#1#2', '8#40#1#2']], $doble->entrada);
    }

    /** Sin ninguna cápsula válida no se borra nada, pero responde éxito (no «Operación no autorizada»). */
    public function test_eliminar_avisos_sin_cápsulas_válidas_no_borra_nada(): void
    {
        $doble = $this->doble(['ok' => true, 'mensaje' => '']);
        $this->sustituirServicio(CambioUsuarioEliminar::class, $doble);

        $json = $this->ejecutar('cambio_usuario_eliminar', ['sel' => ['xxx', '7#1#1#1']]);

        $this->assertTrue($json['success']);
        $this->assertSame(['sel' => []], $doble->entrada);
    }
}
