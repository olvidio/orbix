<?php

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\TipoDocOpcionesData;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;

final class TipoDocOpcionesDataTest extends TestCase
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

    public function test_devuelve_opciones_del_repositorio(): void
    {
        $repo = $this->createMock(TipoDocRepositoryInterface::class);
        $repo->method('getArrayTipoDoc')->willReturn(['x' => 'Tipo X']);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDocRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            ['a_opciones' => ['x' => 'Tipo X']],
            TipoDocOpcionesData::build()
        );
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
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
