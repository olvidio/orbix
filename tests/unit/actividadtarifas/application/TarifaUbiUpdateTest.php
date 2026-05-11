<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TarifaUbiUpdate;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;

final class TarifaUbiUpdateTest extends TestCase
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

    public function test_no_encontrada(): void
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->with(10)->willReturn(null);

        $GLOBALS['container'] = $this->containerOne(TarifaUbiRepositoryInterface::class, $repo);

        $this->assertNotSame('', TarifaUbiUpdate::execute(['id_item' => 10]));
    }

    public function test_actualizar_exito(): void
    {
        $o = $this->createMock(TarifaUbi::class);
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->with(10)->willReturn($o);
        $o->expects($this->once())->method('setCantidad')->with(12.5);
        $repo->expects($this->once())->method('Guardar')->with($o)->willReturn(true);
        $repo->method('getErrorTxt')->willReturn('');

        $GLOBALS['container'] = $this->containerOne(TarifaUbiRepositoryInterface::class, $repo);

        $this->assertSame('', TarifaUbiUpdate::execute([
            'id_item' => 10,
            'cantidad' => '12.5',
        ]));
    }

    public function test_guardar_falla(): void
    {
        $o = $this->createMock(TarifaUbi::class);
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $GLOBALS['container'] = $this->containerOne(TarifaUbiRepositoryInterface::class, $repo);

        $msg = TarifaUbiUpdate::execute(['id_item' => 1]);
        $this->assertStringContainsString('db', $msg);
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
