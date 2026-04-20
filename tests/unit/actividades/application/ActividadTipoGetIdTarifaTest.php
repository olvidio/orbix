<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadTipoGetIdTarifa;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;

final class ActividadTipoGetIdTarifaTest extends TestCase
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

    public function test_sin_resultados_devuelve_cadena_vacia(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('getTipoActivTarifas')->willReturn([]);

        $GLOBALS['container'] = new class($repo) {
            public function __construct(private readonly object $repo) {}

            public function get(string $id): object
            {
                return $this->repo;
            }
        };

        $this->assertSame('', (new ActividadTipoGetIdTarifa())->execute(['entrada' => '123456']));
    }

    public function test_devuelve_id_tarifa_del_primer_elemento(): void
    {
        $rel = new class {
            public function getId_tarifa(): int
            {
                return 42;
            }
        };

        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('getTipoActivTarifas')->willReturn([$rel]);

        $GLOBALS['container'] = new class($repo) {
            public function __construct(private readonly object $repo) {}

            public function get(string $id): object
            {
                return $this->repo;
            }
        };

        $this->assertSame('42', (new ActividadTipoGetIdTarifa())->execute(['entrada' => '123456']));
    }
}
