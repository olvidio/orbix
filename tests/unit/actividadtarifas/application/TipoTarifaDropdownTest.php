<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;

final class TipoTarifaDropdownTest extends TestCase
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

    public function test_devuelve_array_del_repo(): void
    {
        $map = [1 => 'A', 2 => 'B'];
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->expects($this->once())->method('getArrayTipoTarifas')->with('')->willReturn($map);

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $this->assertSame($map, TipoTarifaDropdown::opciones(0));
    }

    public function test_filtra_por_sfsv(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->expects($this->once())->method('getArrayTipoTarifas')->with(2)->willReturn([]);

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $this->assertSame([], TipoTarifaDropdown::opciones(2));
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
