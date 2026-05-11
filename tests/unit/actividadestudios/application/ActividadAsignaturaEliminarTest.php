<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\ActividadAsignaturaEliminar;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;

final class ActividadAsignaturaEliminarTest extends TestCase
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

    public function test_pau_distinto_de_a_devuelve_mensaje(): void
    {
        $repo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerOne(ActividadAsignaturaDlRepositoryInterface::class, $repo);

        $msg = ActividadAsignaturaEliminar::execute(['pau' => 'p', 'id_activ' => 1, 'id_asignatura' => 2]);
        $this->assertNotSame('', $msg);
    }

    public function test_faltan_claves_devuelve_mensaje(): void
    {
        $repo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerOne(ActividadAsignaturaDlRepositoryInterface::class, $repo);

        $msg = ActividadAsignaturaEliminar::execute(['pau' => 'a', 'id_activ' => 0, 'id_asignatura' => 2]);
        $this->assertStringContainsString('faltan', $msg);
    }

    public function test_no_existe_asignatura(): void
    {
        $repo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $repo->method('findById')->with(10, 20)->willReturn(null);

        $GLOBALS['container'] = $this->containerOne(ActividadAsignaturaDlRepositoryInterface::class, $repo);

        $msg = ActividadAsignaturaEliminar::execute([
            'pau' => 'a',
            'id_activ' => 10,
            'id_asignatura' => 20,
        ]);
        $this->assertStringContainsString('no encuentro', $msg);
    }

    public function test_eliminar_falla_devuelve_error(): void
    {
        $oAa = $this->createMock(ActividadAsignatura::class);
        $repo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $repo->method('findById')->willReturn($oAa);
        $repo->method('Eliminar')->with($oAa)->willReturn(false);

        $GLOBALS['container'] = $this->containerOne(ActividadAsignaturaDlRepositoryInterface::class, $repo);

        $msg = ActividadAsignaturaEliminar::execute([
            'pau' => 'a',
            'id_activ' => 10,
            'id_asignatura' => 20,
        ]);
        $this->assertStringContainsString('no se ha borrado', $msg);
    }

    public function test_exito_parseando_sel(): void
    {
        $oAa = $this->createMock(ActividadAsignatura::class);
        $repo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $repo->method('findById')->with(7, 8)->willReturn($oAa);
        $repo->expects($this->once())->method('Eliminar')->with($oAa)->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(ActividadAsignaturaDlRepositoryInterface::class, $repo);

        $msg = ActividadAsignaturaEliminar::execute([
            'pau' => 'a',
            'sel' => ['7#8'],
        ]);
        $this->assertSame('', $msg);
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
