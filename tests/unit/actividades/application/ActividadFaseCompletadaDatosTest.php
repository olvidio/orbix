<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadFaseCompletadaDatos;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

final class ActividadFaseCompletadaDatosTest extends TestCase
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

    public function test_ids_no_positivos_devuelve_false_sin_repo(): void
    {
        $this->assertSame(['completada' => false], (new ActividadFaseCompletadaDatos())->ejecutar(0, 1));
        $this->assertSame(['completada' => false], (new ActividadFaseCompletadaDatos())->ejecutar(1, 0));
    }

    public function test_delega_en_repositorio(): void
    {
        $repo = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('faseCompletada')
            ->with(10, 3)
            ->willReturn(true);

        $GLOBALS['container'] = new class($repo) {
            public function __construct(private readonly ActividadProcesoTareaRepositoryInterface $repo) {}

            public function get(string $key): object
            {
                if ($key !== ActividadProcesoTareaRepositoryInterface::class) {
                    throw new \RuntimeException('Clave inesperada: ' . $key);
                }

                return $this->repo;
            }
        };

        $out = (new ActividadFaseCompletadaDatos())->ejecutar(10, 3);

        $this->assertSame(['completada' => true], $out);
    }
}
