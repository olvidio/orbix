<?php

declare(strict_types=1);

namespace Tests\integration\devel_codegen\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/**
 * Endpoint que da al generador de código la estructura de una tabla.
 */
final class TablaEstructuraDatosControllerTest extends myTest
{
    private const CONTROLADOR = __DIR__ . '/../../../../../src/devel_codegen/infrastructure/ui/http/controllers/tabla_estructura_datos.php';

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
    private function json(array $post): array
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

    public function test_da_la_clave_primaria_las_columnas_y_la_fecha(): void
    {
        $json = $this->json(['db' => 'comun', 'tabla' => 'H-dlb.u_cdc_dl']);

        $this->assertTrue($json['success'], (string) ($json['mensaje'] ?? ''));
        $data = json_decode((string) $json['data'], true);
        $this->assertSame(['id_ubi'], $data['primary_key']);
        $this->assertContains('nombre_ubi', array_column($data['columnas'], 'field'));
        $this->assertNotSame('', $data['hoy']);
    }

    public function test_sin_parametros_o_con_una_base_de_datos_que_no_existe_es_un_error(): void
    {
        $this->assertFalse($this->json([])['success']);
        $this->assertFalse($this->json(['db' => 'otra', 'tabla' => 'public.x'])['success']);
    }
}
