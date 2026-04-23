<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadessacd\application\SacdEliminar;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\entity\Asistente;

/**
 * Unitarios del use case {@see SacdEliminar}: cubre validaciones, borrado
 * del `ActividadCargo`, borrado de la `Asistencia` asociada cuando existe y
 * propagacion de errores (`Eliminar === false`).
 */
final class SacdEliminarTest extends TestCase
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

    public function test_sin_id_activ_devuelve_error(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = SacdEliminar::execute(['id_activ' => 0, 'id_cargo' => 2001, 'id_nom' => 111]);
        $this->assertStringContainsString('no se sabe cual borrar', $out);
    }

    public function test_sin_id_cargo_devuelve_error(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = SacdEliminar::execute(['id_activ' => 500, 'id_cargo' => 0, 'id_nom' => 111]);
        $this->assertStringContainsString('no se sabe cual borrar', $out);
    }

    public function test_elimina_cargo_existente_y_asistencia(): void
    {
        $oCargo = new ActividadCargo();
        $oCargo->setId_activ(500);
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')
            ->with(['id_activ' => 500, 'id_cargo' => 2001])
            ->willReturn([$oCargo]);
        $activCargoRepo->expects($this->once())
            ->method('Eliminar')
            ->with($oCargo)
            ->willReturn(true);

        $oAsistencia = new Asistente();
        $oAsistencia->setId_activ(500);
        $oAsistencia->setId_nom(111);

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('findById')->with(500, 111)->willReturn($oAsistencia);
        $asistenteRepo->expects($this->once())
            ->method('Eliminar')
            ->with($oAsistencia)
            ->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdEliminar::execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 111,
        ]);
        $this->assertSame('', $out);
    }

    public function test_id_nom_cero_no_toca_asistencia(): void
    {
        $oCargo = new ActividadCargo();
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$oCargo]);
        $activCargoRepo->method('Eliminar')->willReturn(true);

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->expects($this->never())->method('findById');
        $asistenteRepo->expects($this->never())->method('Eliminar');

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdEliminar::execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 0,
        ]);
        $this->assertSame('', $out);
    }

    public function test_error_si_no_elimina_cargo(): void
    {
        $oCargo = new ActividadCargo();
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$oCargo]);
        $activCargoRepo->method('Eliminar')->willReturn(false);

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('findById')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdEliminar::execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 111,
        ]);
        $this->assertStringContainsString('no se ha eliminado el cargo', $out);
    }

    public function test_error_si_no_elimina_asistencia(): void
    {
        $oCargo = new ActividadCargo();
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$oCargo]);
        $activCargoRepo->method('Eliminar')->willReturn(true);

        $oAsistencia = new Asistente();

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('findById')->willReturn($oAsistencia);
        $asistenteRepo->method('Eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdEliminar::execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 111,
        ]);
        $this->assertStringContainsString('no se ha eliminado la asistencia', $out);
    }

    public function test_cargo_inexistente_no_rompe_y_sigue_con_asistencia(): void
    {
        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([]);
        $activCargoRepo->expects($this->never())->method('Eliminar');

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('findById')->willReturn(null);
        $asistenteRepo->expects($this->never())->method('Eliminar');

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdEliminar::execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 111,
        ]);
        $this->assertSame('', $out);
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
