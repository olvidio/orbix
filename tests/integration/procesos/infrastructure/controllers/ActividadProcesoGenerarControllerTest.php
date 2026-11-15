<?php

declare(strict_types=1);

namespace Tests\integration\procesos\infrastructure\controllers;

use src\procesos\application\ActividadProcesoGenerar;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * `/src/procesos/actividad_proceso_generar` vuelve a crear el proceso (las fases) de una actividad:
 * solo con la cápsula que firma la pantalla del proceso (`actividad_proceso_data`), y la actividad
 * sale de la cápsula, no del POST. Fase 6.5 del plan de revisión de tests.
 */
final class ActividadProcesoGenerarControllerTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const CONTROLADOR = __DIR__ . '/../../../../../src/procesos/infrastructure/ui/http/controllers/actividad_proceso_generar.php';

    /** @var array<string, mixed> */
    private array $postAnterior = [];
    private object $caso;

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
        $this->caso = new class() {
            /** @var list<array<string, mixed>> */
            public array $entradas = [];

            /** @param array<string, mixed> $input */
            public function execute(array $input): string
            {
                $this->entradas[] = $input;

                return '';
            }
        };
        $this->sustituirServicio(ActividadProcesoGenerar::class, $this->caso);
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
    private function ejecutar(array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::CONTROLADOR;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, 'debe emitir solo JSON; emitió: ' . substr($salida, 0, 200));

        return $json;
    }

    public function test_sin_la_capsula_de_la_pantalla_no_se_genera_el_proceso(): void
    {
        foreach (['sin cápsula' => [], 'falsa' => ['ctx_generar' => 'x'], 'de otra acción' => ['ctx_generar' => HashB::sign('actividad_proceso_update', ['id_activ' => 3001])]] as $txt => $post) {
            $json = $this->ejecutar($post + ['id_activ' => '3001']);
            $this->assertFalse($json['success'], $txt);
            $this->assertSame(_("Operación no autorizada"), $json['mensaje'], $txt);
        }
        $this->assertSame([], $this->caso->entradas);
    }

    public function test_con_la_capsula_se_genera_el_de_la_actividad_que_ella_fija(): void
    {
        $json = $this->ejecutar(['ctx_generar' => HashB::sign('actividad_proceso_generar', ['id_activ' => 3001]), 'id_activ' => '9999']);

        $this->assertTrue($json['success']);
        $this->assertSame(3001, $this->caso->entradas[0]['id_activ'] ?? null);
    }
}
