<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoVerEliminar;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;

final class EncargoVerEliminarTest extends TestCase
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

    public function test_sel_vacio_devuelve_sin_error(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $this->createMock(EncargoRepositoryInterface::class),
        ]);

        $this->assertSame(['error' => ''], EncargoVerEliminar::execute([]));
        $this->assertSame(['error' => ''], EncargoVerEliminar::execute(['sel' => []]));
    }

    public function test_encargo_no_encontrado(): void
    {
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->with(12)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $rta = EncargoVerEliminar::execute(['sel' => ['12#extra']]);
        $this->assertNotSame('', $rta['error']);
    }

    public function test_falla_eliminar(): void
    {
        $enc = $this->createMock(Encargo::class);

        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->willReturn($enc);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $rta = EncargoVerEliminar::execute(['sel' => ['5#']]);
        $this->assertNotSame('', $rta['error']);
        $this->assertStringContainsString('db', $rta['error']);
    }

    public function test_exito(): void
    {
        $enc = $this->createMock(Encargo::class);

        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->with(99)->willReturn($enc);
        $repo->method('Eliminar')->with($enc)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['error' => ''], EncargoVerEliminar::execute(['sel' => ['99#x']]));
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
