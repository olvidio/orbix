<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadFasesCompletadasDatos;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

final class ActividadFasesCompletadasDatosTest extends TestCase
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

    public function test_id_no_positivo_lista_vacia(): void
    {
        $out = (new ActividadFasesCompletadasDatos())->ejecutar(0);

        $this->assertSame(['fases_completadas' => []], $out);
    }

    public function test_mapea_ids_a_enteros(): void
    {
        $repo = $this->createStub(ActividadProcesoTareaRepositoryInterface::class);
        $repo->method('getFasesCompletadas')->with(7)->willReturn(['1', 2, '5']);

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

        $out = (new ActividadFasesCompletadasDatos())->ejecutar(7);

        $this->assertSame(['fases_completadas' => [1, 2, 5]], $out);
    }
}
