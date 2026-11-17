<?php

declare(strict_types=1);

namespace Tests\integration\actividadestudios\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;

/**
 * `docencia_actualizar` responde el mensaje final en `data.txt_rta`, que es lo que lee
 * `frontend/actividadestudios/controller/actualizar_docencia.php` (un texto suelto en `data`
 * le llega a `PostRequest` como `[]` y la pantalla salía en blanco).
 */
final class DocenciaActualizarControllerTest extends myTest
{
    private const CONTROLADOR = __DIR__ . '/../../../../../src/actividadestudios/infrastructure/ui/http/controllers/docencia_actualizar.php';

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
    }

    public function tearDown(): void
    {
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
        $this->assertIsArray($json, 'docencia_actualizar debe emitir solo JSON; emitió: ' . substr($salida, 0, 200));

        return $json;
    }

    public function test_el_mensaje_final_llega_en_txt_rta(): void
    {
        // Un curso sin actividades terminadas: no se graba nada.
        $json = $this->ejecutar([
            'ctx_actualizar' => HashB::sign('docencia_actualizar'),
            'periodo' => 'curso_ca',
            'year' => '1950',
        ]);

        $this->assertTrue($json['success']);
        $data = json_decode((string) $json['data'], true);
        $this->assertIsArray($data, 'data debe ser un objeto JSON, no un texto suelto');
        $this->assertStringStartsWith(sprintf(_("Se ha actualizado la docencia para el periodo: %s"), ''), (string) ($data['txt_rta'] ?? ''));
    }
}
