<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosSelectData;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;

final class ProcesosSelectDataTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_devuelve_array_tipos_desde_repositorio(): void
    {
        $repo = $this->createMock(ProcesoTipoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayProcesoTipos')
            ->willReturn([1 => 'Tipo A', 2 => 'Tipo B']);

        $GLOBALS['container'] = $this->containerFromMap([
            ProcesoTipoRepositoryInterface::class => $repo,
        ]);

        $out = ProcesosSelectData::execute();
        $this->assertSame([1 => 'Tipo A', 2 => 'Tipo B'], $out['a_tipos_proceso']);
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
