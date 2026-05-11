<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TipoTarifaEliminar;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;

final class TipoTarifaEliminarTest extends TestCase
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
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $this->assertNotSame('', TipoTarifaEliminar::execute(['id_tarifa' => 0]));
    }

    public function test_no_encontrada(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $this->assertNotSame('', TipoTarifaEliminar::execute(['id_tarifa' => 5]));
    }

    public function test_exito(): void
    {
        $o = $this->createMock(TipoTarifa::class);
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->expects($this->once())->method('Eliminar')->with($o)->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $this->assertSame('', TipoTarifaEliminar::execute(['id_tarifa' => 5]));
    }

    public function test_falla_eliminar(): void
    {
        $o = $this->createMock(TipoTarifa::class);
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->method('Eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $this->assertNotSame('', TipoTarifaEliminar::execute(['id_tarifa' => 5]));
    }

    /**
     * @param class-string $iface
     */
    private function containerOne(string $iface, object $service): object
    {
        return new class($iface, $service) {
            public function __construct(
                private readonly string $iface,
                private readonly object $service
            ) {}

            public function get(string $id): object
            {
                if ($id !== $this->iface) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->service;
            }
        };
    }
}
