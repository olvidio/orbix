<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadessacd\application\SacdReordenar;

/**
 * Unitarios del use case {@see SacdReordenar}: mockea los 2 repos via
 * contenedor DI. Cubre validaciones de entrada, las dos direcciones
 * (`mas` / `menos`), los bordes (no hay anterior / posterior, vecino con
 * `id_nom = 0` no se intercambia) y la propagacion de errores de Guardar.
 */
final class SacdReordenarTest extends TestCase
{
        public function test_sin_id_activ_devuelve_error(): void {
        $out = (new \src\actividadessacd\application\SacdReordenar($this->createMock(\src\actividadcargos\domain\contracts\CargoRepositoryInterface::class), $this->createMock(\src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface::class)))->execute([
            'id_activ' => 0,
            'id_nom' => 111,
            'num_orden' => 'mas',
        ]);
        $this->assertStringContainsString('faltan parametros', $out);
    }

    public function test_direccion_invalida_devuelve_error(): void {
        $out = (new \src\actividadessacd\application\SacdReordenar($this->createMock(\src\actividadcargos\domain\contracts\CargoRepositoryInterface::class), $this->createMock(\src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface::class)))->execute([
            'id_activ' => 500,
            'id_nom' => 111,
            'num_orden' => 'invertir',
        ]);
        $this->assertStringContainsString('direccion de orden', $out);
    }

    public function test_mas_intercambia_id_nom_con_cargo_anterior(): void {
        $cargo1 = new ActividadCargo();
        $cargo1->setId_cargo(2001);
        $cargo1->setId_nom(222);

        $cargo2 = new ActividadCargo();
        $cargo2->setId_cargo(2002);
        $cargo2->setId_nom(111);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->with('sacd')->willReturn([
            2001 => 'sacd1',
            2002 => 'sacd2',
        ]);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$cargo1, $cargo2]);
        $activCargoRepo->expects($this->exactly(2))->method('Guardar')->willReturn(true);

        $out = (new \src\actividadessacd\application\SacdReordenar($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 500,
            'id_nom' => 111,
            'num_orden' => 'mas',
        ]);
        $this->assertSame('', $out);
        $this->assertSame(111, $cargo1->getId_nom());
        $this->assertSame(222, $cargo2->getId_nom());
    }

    public function test_menos_intercambia_id_nom_con_cargo_posterior(): void {
        $cargo1 = new ActividadCargo();
        $cargo1->setId_cargo(2001);
        $cargo1->setId_nom(111);

        $cargo2 = new ActividadCargo();
        $cargo2->setId_cargo(2002);
        $cargo2->setId_nom(222);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'a', 2002 => 'b']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$cargo1, $cargo2]);
        $activCargoRepo->expects($this->exactly(2))->method('Guardar')->willReturn(true);

        $out = (new \src\actividadessacd\application\SacdReordenar($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 500,
            'id_nom' => 111,
            'num_orden' => 'menos',
        ]);
        $this->assertSame('', $out);
        $this->assertSame(222, $cargo1->getId_nom());
        $this->assertSame(111, $cargo2->getId_nom());
    }

    public function test_mas_en_primera_posicion_no_hace_nada(): void {
        $cargo1 = new ActividadCargo();
        $cargo1->setId_cargo(2001);
        $cargo1->setId_nom(111);

        $cargo2 = new ActividadCargo();
        $cargo2->setId_cargo(2002);
        $cargo2->setId_nom(222);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'a', 2002 => 'b']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$cargo1, $cargo2]);
        $activCargoRepo->expects($this->never())->method('Guardar');

        $out = (new \src\actividadessacd\application\SacdReordenar($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 500,
            'id_nom' => 111,
            'num_orden' => 'mas',
        ]);
        $this->assertSame('', $out);
    }

    public function test_menos_en_ultima_posicion_no_hace_nada(): void {
        $cargo1 = new ActividadCargo();
        $cargo1->setId_cargo(2001);
        $cargo1->setId_nom(222);

        $cargo2 = new ActividadCargo();
        $cargo2->setId_cargo(2002);
        $cargo2->setId_nom(111);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'a', 2002 => 'b']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$cargo1, $cargo2]);
        $activCargoRepo->expects($this->never())->method('Guardar');

        $out = (new \src\actividadessacd\application\SacdReordenar($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 500,
            'id_nom' => 111,
            'num_orden' => 'menos',
        ]);
        $this->assertSame('', $out);
    }

    public function test_id_nom_no_encontrado_no_hace_nada(): void {
        $cargo1 = new ActividadCargo();
        $cargo1->setId_cargo(2001);
        $cargo1->setId_nom(222);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'a']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$cargo1]);
        $activCargoRepo->expects($this->never())->method('Guardar');

        $out = (new \src\actividadessacd\application\SacdReordenar($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 500,
            'id_nom' => 999,
            'num_orden' => 'menos',
        ]);
        $this->assertSame('', $out);
    }

    public function test_vecino_con_id_nom_cero_no_se_intercambia(): void {
        $cargo1 = new ActividadCargo();
        $cargo1->setId_cargo(2001);
        $cargo1->setId_nom(0);

        $cargo2 = new ActividadCargo();
        $cargo2->setId_cargo(2002);
        $cargo2->setId_nom(111);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'a', 2002 => 'b']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$cargo1, $cargo2]);
        $activCargoRepo->expects($this->never())->method('Guardar');

        $out = (new \src\actividadessacd\application\SacdReordenar($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 500,
            'id_nom' => 111,
            'num_orden' => 'mas',
        ]);
        $this->assertSame('', $out);
    }

    public function test_error_si_guardar_falla(): void {
        $cargo1 = new ActividadCargo();
        $cargo1->setId_cargo(2001);
        $cargo1->setId_nom(222);

        $cargo2 = new ActividadCargo();
        $cargo2->setId_cargo(2002);
        $cargo2->setId_nom(111);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'a', 2002 => 'b']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$cargo1, $cargo2]);
        $activCargoRepo->method('Guardar')->willReturn(false);

        $out = (new \src\actividadessacd\application\SacdReordenar($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 500,
            'id_nom' => 111,
            'num_orden' => 'mas',
        ]);
        $this->assertStringContainsString('no se ha guardado', $out);
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
