<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\procesos\application\TipoActivProcesoAsignar;

/**
 * Solo la rama `propio` falsa: usa {@see TipoDeActividad::setId_tipo_proceso_ex}.
 */
final class TipoActivProcesoAsignarTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['sfsv' => 2]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_exito_proceso_no_propio(): void
    {
        $tipo = $this->createMock(TipoDeActividad::class);
        $tipo->expects($this->once())
            ->method('setId_tipo_proceso_ex')
            ->with(7, 2);

        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('findById')->with(100)->willReturn($tipo);
        $repo->expects($this->once())->method('Guardar')->with($tipo)->willReturn(true);

        $this->assertSame('', (new TipoActivProcesoAsignar($repo))->execute([
            'id_tipo_activ' => 100,
            'propio' => 'f',
            'id_tipo_proceso' => 7,
        ]));
    }

    public function test_falla_guardar(): void
    {
        $tipo = $this->createMock(TipoDeActividad::class);
        $tipo->method('setId_tipo_proceso_ex');

        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('findById')->willReturn($tipo);
        $repo->method('Guardar')->willReturn(false);

        $this->assertNotSame('', (new TipoActivProcesoAsignar($repo))->execute([
            'id_tipo_activ' => 1,
            'propio' => '',
            'id_tipo_proceso' => 2,
        ]));
    }
}
