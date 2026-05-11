<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\RelacionTarifaEliminar;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;

final class RelacionTarifaEliminarTest extends TestCase
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
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerOne(RelacionTarifaTipoActividadRepositoryInterface::class, $repo);

        $this->assertNotSame('', RelacionTarifaEliminar::execute(['id_item' => 0]));
    }

    public function test_exito(): void
    {
        $o = $this->createMock(RelacionTarifaTipoActividad::class);
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn($o);
        $repo->expects($this->once())->method('Eliminar')->with($o)->willReturn(true);
        $repo->method('getErrorTxt')->willReturn('');

        $GLOBALS['container'] = $this->containerOne(RelacionTarifaTipoActividadRepositoryInterface::class, $repo);

        $this->assertSame('', RelacionTarifaEliminar::execute(['id_item' => 3]));
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
