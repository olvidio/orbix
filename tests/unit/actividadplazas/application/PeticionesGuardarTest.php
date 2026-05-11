<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PeticionesGuardar;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;

final class PeticionesGuardarTest extends TestCase
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

        $msg = PeticionesGuardar::execute(['id_nom' => 1, 'sactividad' => '']);
        $this->assertNotSame('', $msg);
    }

    public function test_borra_anteriores_y_crea_nuevas(): void
    {
        $vieja = $this->createMock(PlazaPeticion::class);

        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('getPlazasPeticion')->willReturn([$vieja]);
        $repo->expects($this->once())->method('Eliminar')->with($vieja);

        $repo->method('findById')->willReturn(null);
        $repo->expects($this->exactly(2))->method('Guardar')->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(PlazaPeticionRepositoryInterface::class, $repo);

        $msg = PeticionesGuardar::execute([
            'id_nom' => 100,
            'sactividad' => 'ca',
            'actividades' => [10, 0, 20],
        ]);
        $this->assertSame('', $msg);
    }

    public function test_reutiliza_fila_existente_por_findById(): void
    {
        $existente = new PlazaPeticion();
        $existente->setId_nom(100);
        $existente->setId_activ(10);
        $existente->setOrden(9);
        $existente->setTipo('x');

        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('getPlazasPeticion')->willReturn([]);
        $repo->method('findById')->with(100, 10)->willReturn($existente);
        $repo->expects($this->once())->method('Guardar')->with($this->callback(function (PlazaPeticion $p) {
            return $p->getId_nom() === 100
                && $p->getId_activ() === 10
                && $p->getOrden() === 1
                && $p->getTipo() === 'ca';
        }))->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(PlazaPeticionRepositoryInterface::class, $repo);

        $this->assertSame('', PeticionesGuardar::execute([
            'id_nom' => 100,
            'sactividad' => 'ca',
            'actividades' => [10],
        ]));
    }

    public function test_error_si_guardar_falla(): void
    {
        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('getPlazasPeticion')->willReturn([]);
        $repo->method('findById')->willReturn(null);
        $repo->method('Guardar')->willReturn(false);

        $GLOBALS['container'] = $this->containerOne(PlazaPeticionRepositoryInterface::class, $repo);

        $msg = PeticionesGuardar::execute([
            'id_nom' => 1,
            'sactividad' => 'ca',
            'actividades' => [5],
        ]);
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
