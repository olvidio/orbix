<?php

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\cambios\application\CambioUsuarioObjetoPrefEliminar;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;

final class CambioUsuarioObjetoPrefEliminarTest extends TestCase
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

    public function test_sin_id(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioObjetoPrefRepositoryInterface::class => $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class),
        ]);

        $rta = CambioUsuarioObjetoPrefEliminar::execute([]);
        $this->assertNotSame('', $rta['error']);
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioObjetoPrefRepositoryInterface::class => $repo,
        ]);

        $rta = CambioUsuarioObjetoPrefEliminar::execute(['id_item_usuario_objeto' => 5]);
        $this->assertNotSame('', $rta['error']);
    }

    public function test_falla_eliminar(): void
    {
        $pref = $this->createMock(CambioUsuarioObjetoPref::class);

        $repo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $repo->method('findById')->willReturn($pref);
        $repo->method('Eliminar')->with($pref)->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioObjetoPrefRepositoryInterface::class => $repo,
        ]);

        $rta = CambioUsuarioObjetoPrefEliminar::execute(['id_item_usuario_objeto' => 1]);
        $this->assertNotSame('', $rta['error']);
    }

    public function test_exito(): void
    {
        $pref = $this->createMock(CambioUsuarioObjetoPref::class);

        $repo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $repo->method('findById')->willReturn($pref);
        $repo->method('Eliminar')->with($pref)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioObjetoPrefRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            ['error' => ''],
            CambioUsuarioObjetoPrefEliminar::execute(['id_item_usuario_objeto' => 99])
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
