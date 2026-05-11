<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PeticionesEliminar;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;

final class PeticionesEliminarTest extends TestCase
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

    public function test_faltan_parametros(): void
    {
        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->expects($this->never())->method('getPlazasPeticion');

        $GLOBALS['container'] = $this->containerOne(PlazaPeticionRepositoryInterface::class, $repo);

        $msg = PeticionesEliminar::execute(['id_nom' => 0, 'sactividad' => 'ca']);
        $this->assertNotSame('', $msg);
    }

    public function test_elimina_todas_y_exito(): void
    {
        $p1 = $this->createMock(PlazaPeticion::class);
        $p2 = $this->createMock(PlazaPeticion::class);

        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('getPlazasPeticion')->with([
            'id_nom' => 9,
            'tipo' => 'ca',
        ])->willReturn([$p1, $p2]);
        $repo->expects($this->exactly(2))->method('Eliminar')
            ->willReturnOnConsecutiveCalls(true, true);

        $GLOBALS['container'] = $this->containerOne(PlazaPeticionRepositoryInterface::class, $repo);

        $this->assertSame('', PeticionesEliminar::execute(['id_nom' => 9, 'sactividad' => 'ca']));
    }

    public function test_error_si_falla_eliminar(): void
    {
        $p1 = $this->createMock(PlazaPeticion::class);
        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('getPlazasPeticion')->willReturn([$p1]);
        $repo->method('Eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerOne(PlazaPeticionRepositoryInterface::class, $repo);

        $msg = PeticionesEliminar::execute(['id_nom' => 1, 'sactividad' => 'crt']);
        $this->assertNotSame('', $msg);
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
