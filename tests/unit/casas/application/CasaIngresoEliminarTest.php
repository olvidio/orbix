<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\CasaIngresoEliminar;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\entity\Ingreso;

final class CasaIngresoEliminarTest extends TestCase
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

    public function test_sin_id_activ(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            IngresoRepositoryInterface::class => $this->createMock(IngresoRepositoryInterface::class),
        ]);

        $rta = CasaIngresoEliminar::execute([]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_ingreso_no_encontrado(): void
    {
        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            IngresoRepositoryInterface::class => $repo,
        ]);

        $rta = CasaIngresoEliminar::execute(['id_activ' => 5]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_falla_eliminar(): void
    {
        $ing = $this->createMock(Ingreso::class);

        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->willReturn($ing);
        $repo->method('Eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            IngresoRepositoryInterface::class => $repo,
        ]);

        $rta = CasaIngresoEliminar::execute(['id_activ' => 1]);
        $this->assertFalse($rta['ok']);
    }

    public function test_exito(): void
    {
        $ing = $this->createMock(Ingreso::class);

        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->willReturn($ing);
        $repo->method('Eliminar')->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            IngresoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            ['ok' => true, 'mensaje' => '', 'data' => ''],
            CasaIngresoEliminar::execute(['id_activ' => 2])
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
