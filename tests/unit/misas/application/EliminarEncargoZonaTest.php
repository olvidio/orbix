<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\misas\application\EliminarEncargoZona;

final class EliminarEncargoZonaTest extends TestCase
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

    public function test_encargo_no_encontrado(): void
    {
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $msg = EliminarEncargoZona::execute(9);
        $this->assertStringContainsString('9', $msg);
    }

    public function test_falla_eliminar(): void
    {
        $enc = $this->createMock(Encargo::class);
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->willReturn($enc);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db-err');

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('db-err', EliminarEncargoZona::execute(1));
    }

    public function test_exito(): void
    {
        $enc = $this->createMock(Encargo::class);
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->willReturn($enc);
        $repo->expects($this->once())->method('Eliminar')->with($enc)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', EliminarEncargoZona::execute(3));
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
