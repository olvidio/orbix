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
        public function test_sin_id_activ_devuelve_error(): void {
        $out = (new \src\actividadessacd\application\SacdEliminar($this->createMock(\src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface::class), $this->createMock(\src\asistentes\domain\contracts\AsistenteDlRepositoryInterface::class), $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class)))->execute(['id_activ' => 0, 'id_cargo' => 2001, 'id_nom' => 111]);
        $this->assertStringContainsString('no se sabe cual borrar', $out);
    }

    public function test_sin_id_cargo_devuelve_error(): void {
        $out = (new \src\actividadessacd\application\SacdEliminar($this->createMock(\src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface::class), $this->createMock(\src\asistentes\domain\contracts\AsistenteDlRepositoryInterface::class), $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class)))->execute(['id_activ' => 500, 'id_cargo' => 0, 'id_nom' => 111]);
        $this->assertStringContainsString('no se sabe cual borrar', $out);
    }

    public function test_elimina_cargo_existente_y_asistencia(): void {
        $oCargo = new ActividadCargo();
        $oCargo->setId_activ(500);
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('matching')
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

        $out = (new \src\actividadessacd\application\SacdEliminar($activCargoRepo, $asistenteRepo, $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class)))->execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 111,
        ]);
        $this->assertSame('', $out);
    }

    public function test_id_nom_de_paso_elimina_asistencia(): void {
        $oCargo = new ActividadCargo();
        $oCargo->setId_activ(500);
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('matching')
            ->willReturn([$oCargo]);
        $activCargoRepo->expects($this->once())
            ->method('Eliminar')
            ->with($oCargo)
            ->willReturn(true);

        $oAsistencia = new Asistente();
        $oAsistencia->setId_activ(500);
        $oAsistencia->setId_nom(-42);

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('findById')->with(500, -42)->willReturn($oAsistencia);
        $asistenteRepo->expects($this->once())
            ->method('Eliminar')
            ->with($oAsistencia)
            ->willReturn(true);

        $out = (new SacdEliminar($activCargoRepo, $asistenteRepo, $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class)))->execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => -42,
        ]);
        $this->assertSame('', $out);
    }

    private function cargoRepoConUnCargo(): ActividadCargoRepositoryInterface
    {
        $oCargo = new ActividadCargo();
        $oCargo->setId_activ(500);
        $oCargo->setId_cargo(2001);
        $repo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $repo->method('matching')->willReturn([$oCargo]);
        $repo->method('Eliminar')->willReturn(true);

        return $repo;
    }

    private function asistenciaDePaso(bool $propio): Asistente
    {
        $a = new Asistente();
        $a->setId_activ(500);
        $a->setId_nom(-42);
        $a->setPropio($propio);

        return $a;
    }

    /** La asistencia que creó la asignación del sacd de paso (propio = false) está en `d_asistentes_ex`. */
    public function test_la_asistencia_del_sacd_de_paso_se_borra_de_d_asistentes_ex(): void
    {
        $oAsistencia = $this->asistenciaDePaso(false);
        $dl = $this->createMock(AsistenteDlRepositoryInterface::class);
        $dl->method('findById')->willReturn(null);
        $dl->expects($this->never())->method('Eliminar');
        $ex = $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class);
        $ex->method('findById')->with(500, -42)->willReturn($oAsistencia);
        $ex->expects($this->once())->method('Eliminar')->with($oAsistencia)->willReturn(true);

        $out = (new SacdEliminar($this->cargoRepoConUnCargo(), $dl, $ex))->execute(['id_activ' => 500, 'id_cargo' => 2001, 'id_nom' => -42]);

        $this->assertSame('', $out);
    }

    /** Si la persona de paso asiste por sí misma (propio = true), quitarle el cargo de sacd no borra su asistencia. */
    public function test_la_asistencia_propia_de_una_persona_de_paso_no_se_borra_al_quitarle_el_cargo(): void
    {
        $dl = $this->createMock(AsistenteDlRepositoryInterface::class);
        $dl->method('findById')->willReturn(null);
        $ex = $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class);
        $ex->method('findById')->willReturn($this->asistenciaDePaso(true));
        $ex->expects($this->never())->method('Eliminar');

        $out = (new SacdEliminar($this->cargoRepoConUnCargo(), $dl, $ex))->execute(['id_activ' => 500, 'id_cargo' => 2001, 'id_nom' => -42]);

        $this->assertSame('', $out);
    }

    public function test_id_nom_cero_no_toca_asistencia(): void {
        $oCargo = new ActividadCargo();
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('matching')->willReturn([$oCargo]);
        $activCargoRepo->method('Eliminar')->willReturn(true);

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->expects($this->never())->method('findById');
        $asistenteRepo->expects($this->never())->method('Eliminar');

        $out = (new \src\actividadessacd\application\SacdEliminar($activCargoRepo, $asistenteRepo, $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class)))->execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 0,
        ]);
        $this->assertSame('', $out);
    }

    public function test_error_si_no_elimina_cargo(): void {
        $oCargo = new ActividadCargo();
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('matching')->willReturn([$oCargo]);
        $activCargoRepo->method('Eliminar')->willReturn(false);

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('findById')->willReturn(null);

        $out = (new \src\actividadessacd\application\SacdEliminar($activCargoRepo, $asistenteRepo, $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class)))->execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 111,
        ]);
        $this->assertStringContainsString('no se ha eliminado el cargo', $out);
    }

    public function test_error_si_no_elimina_asistencia(): void {
        $oCargo = new ActividadCargo();
        $oCargo->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('matching')->willReturn([$oCargo]);
        $activCargoRepo->method('Eliminar')->willReturn(true);

        $oAsistencia = new Asistente();

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('findById')->willReturn($oAsistencia);
        $asistenteRepo->method('Eliminar')->willReturn(false);

        $out = (new \src\actividadessacd\application\SacdEliminar($activCargoRepo, $asistenteRepo, $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class)))->execute([
            'id_activ' => 500,
            'id_cargo' => 2001,
            'id_nom' => 111,
        ]);
        $this->assertStringContainsString('no se ha eliminado la asistencia', $out);
    }

    public function test_cargo_inexistente_no_rompe_y_sigue_con_asistencia(): void {
        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('matching')->willReturn([]);
        $activCargoRepo->expects($this->never())->method('Eliminar');

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('findById')->willReturn(null);
        $asistenteRepo->expects($this->never())->method('Eliminar');

        $out = (new \src\actividadessacd\application\SacdEliminar($activCargoRepo, $asistenteRepo, $this->createMock(\src\asistentes\domain\contracts\AsistenteExRepositoryInterface::class)))->execute([
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
