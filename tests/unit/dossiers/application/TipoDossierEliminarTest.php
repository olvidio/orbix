<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\application\TipoDossierEliminar;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;

final class TipoDossierEliminarTest extends TestCase
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
            TipoDossierRepositoryInterface::class => $this->createMock(TipoDossierRepositoryInterface::class),
        ]);

        $this->assertNotSame('', TipoDossierEliminar::execute([]));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDossierRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', TipoDossierEliminar::execute(['id_tipo_dossier' => 5]));
    }

    public function test_falla_eliminar(): void
    {
        $tipo = $this->createMock(TipoDossier::class);

        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->willReturn($tipo);
        $repo->method('Eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDossierRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', TipoDossierEliminar::execute(['id_tipo_dossier' => 1]));
    }

    public function test_exito(): void
    {
        $tipo = $this->createMock(TipoDossier::class);

        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->willReturn($tipo);
        $repo->method('Eliminar')->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDossierRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', TipoDossierEliminar::execute(['id_tipo_dossier' => 9]));
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
