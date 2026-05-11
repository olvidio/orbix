<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ActividadProcesoGenerar;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

final class ActividadProcesoGenerarTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['sfsv' => 1]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_delega_generar_proceso(): void
    {
        $repo = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('generarProceso')
            ->with(50, 1, true);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadProcesoTareaRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', (new ActividadProcesoGenerar())->execute(['id_activ' => 50]));
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
