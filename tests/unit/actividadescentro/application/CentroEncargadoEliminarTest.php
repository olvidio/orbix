<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\CentroEncargadoEliminar;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;

final class CentroEncargadoEliminarTest extends TestCase
{
    private mixed $previousContainer = null;

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

    public function test_parametros_faltantes(): void
    {
        $this->assertNotSame('', CentroEncargadoEliminar::execute(['id_activ' => 0, 'id_ubi' => 1]));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createStub(CentroEncargadoRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $GLOBALS['container'] = new class($repo) {
            public function __construct(private readonly CentroEncargadoRepositoryInterface $repo) {}

            public function get(string $key): object
            {
                if ($key !== CentroEncargadoRepositoryInterface::class) {
                    throw new \RuntimeException('Clave inesperada: ' . $key);
                }

                return $this->repo;
            }
        };

        $this->assertNotSame('', CentroEncargadoEliminar::execute(['id_activ' => 1, 'id_ubi' => 2]));
    }

    public function test_elimina_ok(): void
    {
        $row = new CentroEncargado();
        $row->setId_activ(3);
        $row->setId_ubi(4);

        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->method('findById')->with(3, 4)->willReturn($row);
        $repo->expects($this->once())->method('Eliminar')->with($row)->willReturn(true);

        $GLOBALS['container'] = new class($repo) {
            public function __construct(private readonly CentroEncargadoRepositoryInterface $repo) {}

            public function get(string $key): object
            {
                if ($key !== CentroEncargadoRepositoryInterface::class) {
                    throw new \RuntimeException('Clave inesperada: ' . $key);
                }

                return $this->repo;
            }
        };

        $this->assertSame('', CentroEncargadoEliminar::execute(['id_activ' => 3, 'id_ubi' => 4]));
    }
}
