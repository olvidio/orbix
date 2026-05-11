<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TarifaUbiEliminar;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;

final class TarifaUbiEliminarTest extends TestCase
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
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerOne(TarifaUbiRepositoryInterface::class, $repo);

        $this->assertNotSame('', TarifaUbiEliminar::execute(['id_item' => 0]));
    }

    public function test_exito(): void
    {
        $o = $this->createMock(TarifaUbi::class);
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn($o);
        $repo->expects($this->once())->method('Eliminar')->with($o)->willReturn(true);
        $repo->method('getErrorTxt')->willReturn('');

        $GLOBALS['container'] = $this->containerOne(TarifaUbiRepositoryInterface::class, $repo);

        $this->assertSame('', TarifaUbiEliminar::execute(['id_item' => 9]));
    }

    public function test_falla_eliminar_incluye_error_txt(): void
    {
        $o = $this->createMock(TarifaUbi::class);
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('detalle');

        $GLOBALS['container'] = $this->containerOne(TarifaUbiRepositoryInterface::class, $repo);

        $msg = TarifaUbiEliminar::execute(['id_item' => 1]);
        $this->assertStringContainsString('detalle', $msg);
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
