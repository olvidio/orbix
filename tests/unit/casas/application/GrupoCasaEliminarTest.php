<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\GrupoCasaEliminar;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;

final class GrupoCasaEliminarTest extends TestCase
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

    public function test_sin_id_item(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $this->createMock(GrupoCasaRepositoryInterface::class),
        ]);

        $this->assertNotSame('', GrupoCasaEliminar::execute([]));
    }

    public function test_grupo_no_encontrado(): void
    {
        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', GrupoCasaEliminar::execute(['id_item' => 3]));
    }

    public function test_falla_eliminar(): void
    {
        $grupo = $this->createMock(GrupoCasa::class);

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->willReturn($grupo);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('err db');

        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $repo,
        ]);

        $msg = GrupoCasaEliminar::execute(['id_item' => 1]);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('err db', $msg);
    }

    public function test_exito(): void
    {
        $grupo = $this->createMock(GrupoCasa::class);

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->willReturn($grupo);
        $repo->method('Eliminar')->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            GrupoCasaRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', GrupoCasaEliminar::execute(['id_item' => 9]));
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
