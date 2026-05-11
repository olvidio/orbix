<?php

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\EquipajeEliminar;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;

final class EquipajeEliminarTest extends TestCase
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

    public function test_id_invalido(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            EquipajeRepositoryInterface::class => $this->createMock(EquipajeRepositoryInterface::class),
        ]);

        $this->assertNotSame('', EquipajeEliminar::execute(0));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(EquipajeRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            EquipajeRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', EquipajeEliminar::execute(9));
    }

    public function test_falla_eliminar(): void
    {
        $eq = $this->createMock(Equipaje::class);

        $repo = $this->createMock(EquipajeRepositoryInterface::class);
        $repo->method('findById')->willReturn($eq);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $GLOBALS['container'] = $this->containerFromMap([
            EquipajeRepositoryInterface::class => $repo,
        ]);

        $msg = EquipajeEliminar::execute(3);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('db', $msg);
    }

    public function test_exito(): void
    {
        $eq = $this->createMock(Equipaje::class);

        $repo = $this->createMock(EquipajeRepositoryInterface::class);
        $repo->method('findById')->with(1)->willReturn($eq);
        $repo->method('Eliminar')->with($eq)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            EquipajeRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', EquipajeEliminar::execute(1));
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
