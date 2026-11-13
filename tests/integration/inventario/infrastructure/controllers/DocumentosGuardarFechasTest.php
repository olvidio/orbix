<?php

declare(strict_types=1);

namespace Tests\integration\inventario\infrastructure\controllers;

use src\inventario\application\DocumentosModificar;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Cambio en lote de documentos del inventario: una fecha mal escrita da error
 * y no se guarda nada (antes se tomaba como vacía y borraba la fecha que había).
 */
final class DocumentosGuardarFechasTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const CTRL = __DIR__ . '/../../../../../src/inventario/infrastructure/ui/http/controllers/documentos_guardar.php';

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

    /** @param array<string, mixed> $post @return array<string, mixed> */
    private function ejecutar(array $post): array
    {
        $_POST = $post + ['ctx_guardar' => HashB::sign('documentos_guardar', [])];
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::CTRL;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        return json_decode($salida, true);
    }

    public function test_fecha_no_valida_no_toca_ningun_documento(): void
    {
        $repo = $this->createMock(DocumentoRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');
        $repo->expects($this->never())->method('Guardar');
        $this->sustituirServicio(DocumentoRepositoryInterface::class, $repo);
        // El controlador pide la clase de aplicación al contenedor: si ya estuviera construida con el
        // repositorio real, el doble no llegaría a ella.
        $this->sustituirServicio(DocumentosModificar::class, new DocumentosModificar($repo));

        $json = $this->ejecutar(['documentos' => 'abc#def', 'chk_f_recibido' => 'on', 'f_recibido' => 'ayer']);

        $this->assertFalse($json['success']);
        $this->assertSame('Fecha no válida: ayer', $json['mensaje']);
    }

    public function test_fecha_vacia_sigue_permitiendo_borrarla_y_sin_campos_avisa(): void
    {
        $repo = $this->createMock(DocumentoRepositoryInterface::class);
        $this->sustituirServicio(DocumentoRepositoryInterface::class, $repo);
        // El controlador pide la clase de aplicación al contenedor: si ya estuviera construida con el
        // repositorio real, el doble no llegaría a ella.
        $this->sustituirServicio(DocumentosModificar::class, new DocumentosModificar($repo));
        $json = $this->ejecutar(['documentos' => 'zz', 'chk_f_recibido' => 'on', 'f_recibido' => '']);
        $this->assertNotSame('Fecha no válida: ', $json['mensaje'] ?? '');
        $json = $this->ejecutar(['documentos' => 'zz']);
        $this->assertSame('Debe marcar al menos un campo a modificar', $json['mensaje']);
    }
}
