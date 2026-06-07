<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ActividadProcesoGenerar;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

final class ActividadProcesoGenerarTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['sfsv' => 1]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_delega_generar_proceso(): void
    {
        $repo = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('generarProceso')
            ->with('50', 1, true);

        $useCase = new ActividadProcesoGenerar($repo);
        $this->assertSame('', $useCase->execute(['id_activ' => 50]));
    }
}
