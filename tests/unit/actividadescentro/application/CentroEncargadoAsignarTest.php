<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\application;

use PHPUnit\Framework\TestCase;
use src\actividadescentro\application\CentroEncargadoAsignar;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;

final class CentroEncargadoAsignarTest extends TestCase
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
        $this->assertNotSame('', CentroEncargadoAsignar::execute(['id_activ' => 0, 'id_ubi' => 1]));
        $this->assertNotSame('', CentroEncargadoAsignar::execute(['id_activ' => 1, 'id_ubi' => 0]));
    }

    public function test_primer_centro_num_orden_1(): void
    {
        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->method('getCentrosEncargados')->willReturn([]);
        $repo->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(function (CentroEncargado $e): bool {
                return $e->getId_activ() === 3
                    && $e->getId_ubi() === 7
                    && (int) $e->getNum_orden() === 1
                    && $e->getEncargo() === 'organizador';
            }))
            ->willReturn(true);

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

        $this->assertSame('', CentroEncargadoAsignar::execute(['id_activ' => 3, 'id_ubi' => 7]));
    }

    public function test_incrementa_num_orden(): void
    {
        $existente = new CentroEncargado();
        $existente->setNum_orden(5);

        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->method('getCentrosEncargados')->willReturn([$existente]);
        $repo->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(function (CentroEncargado $e): bool {
                return (int) $e->getNum_orden() === 6;
            }))
            ->willReturn(true);

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

        $this->assertSame('', CentroEncargadoAsignar::execute(['id_activ' => 1, 'id_ubi' => 2]));
    }
}
