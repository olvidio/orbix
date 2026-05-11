<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartaPresentacionEliminar;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;

final class CartaPresentacionEliminarTest extends TestCase
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

    public function test_faltan_ids(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            CartaPresentacionRepositoryInterface::class => $this->createMock(CartaPresentacionRepositoryInterface::class),
        ]);

        $rta = CartaPresentacionEliminar::execute([]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_no_encontrada(): void
    {
        $repo = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $repo->method('findById')->with(3, 4)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            CartaPresentacionRepositoryInterface::class => $repo,
        ]);

        $rta = CartaPresentacionEliminar::execute(['id_ubi' => 3, 'id_direccion' => 4]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_falla_eliminar(): void
    {
        $carta = $this->createMock(CartaPresentacion::class);

        $repo = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $repo->method('findById')->willReturn($carta);
        $repo->method('Eliminar')->with($carta)->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            CartaPresentacionRepositoryInterface::class => $repo,
        ]);

        $rta = CartaPresentacionEliminar::execute(['id_ubi' => 1, 'id_direccion' => 2]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_exito(): void
    {
        $carta = $this->createMock(CartaPresentacion::class);

        $repo = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $repo->method('findById')->willReturn($carta);
        $repo->method('Eliminar')->with($carta)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            CartaPresentacionRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            CartaPresentacionEliminar::execute(['id_ubi' => 9, 'id_direccion' => 8])
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
