<?php

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\ColeccionesOpcionesData;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;

final class ColeccionesOpcionesDataTest extends TestCase
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
        $repo = $this->createMock(ColeccionRepositoryInterface::class);
        $repo->method('getArrayColecciones')->willReturn(['1' => 'A', '2' => 'B']);

        $GLOBALS['container'] = $this->containerFromMap([
            ColeccionRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            ['a_opciones' => ['1' => 'A', '2' => 'B']],
            ColeccionesOpcionesData::build()
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
