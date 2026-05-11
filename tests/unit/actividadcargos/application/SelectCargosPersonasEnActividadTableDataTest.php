<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\SelectCargosPersonasEnActividadTableData;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\Cargo;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;

final class SelectCargosPersonasEnActividadTableDataTest extends TestCase
{
    public function test_permiso_escritura_incluye_sel_con_id_item_y_elim_asis(): void
    {
        $fila = $this->actividadCargoFila(30, 100, 7, true, 'o');
        $cargo = $this->cargoEntity(7, 'd', 'Coord');
        $act = $this->actividadStub('Curso A', 123456);
        $ref = ['123' => ['perm' => true, 'nom' => 'x']];

        $out = SelectCargosPersonasEnActividadTableData::buildValorRows(
            1,
            2,
            [$fila],
            $this->cargoRepoStub([7 => $cargo]),
            $this->actividadRepoStub([100 => $act]),
            $ref,
            null,
            null,
        );

        $this->assertSame([
            1 => [
                'sel' => '30#2',
                1 => 'Coord',
                2 => 'Curso A',
                3 => 'si',
                4 => 'o',
            ],
        ], $out);
    }

    public function test_sin_permiso_sel_vacio(): void
    {
        $fila = $this->actividadCargoFila(31, 101, 8, false, null);
        $cargo = $this->cargoEntity(8, 'd', 'Rol');
        $act = $this->actividadStub('Act B', 999888);
        $ref = ['999' => ['perm' => false, 'nom' => 'y']];

        $out = SelectCargosPersonasEnActividadTableData::buildValorRows(
            1,
            1,
            [$fila],
            $this->cargoRepoStub([8 => $cargo]),
            $this->actividadRepoStub([101 => $act]),
            $ref,
            null,
            null,
        );

        $this->assertSame('', $out[1]['sel']);
    }

    public function test_omite_sacd_en_sf(): void
    {
        $fila = $this->actividadCargoFila(32, 102, 9, false, null);
        $cargo = $this->cargoEntity(9, 'sacd', 'S');

        $stubAct = $this->createMock(ActividadAllRepositoryInterface::class);
        $stubAct->expects($this->never())->method('findById');

        $out = SelectCargosPersonasEnActividadTableData::buildValorRows(
            2,
            1,
            [$fila],
            $this->cargoRepoStub([9 => $cargo]),
            $stubAct,
            [],
            null,
            null,
        );

        $this->assertSame([], $out);
    }

    public function test_select_y_scroll_opcionales(): void
    {
        $fila = $this->actividadCargoFila(33, 103, 1, false, null);
        $act = $this->actividadStub('Z', 111000);

        $out = SelectCargosPersonasEnActividadTableData::buildValorRows(
            1,
            1,
            [$fila],
            $this->cargoRepoStub([1 => $this->cargoEntity(1, 'd', 'C')]),
            $this->actividadRepoStub([103 => $act]),
            [],
            'x',
            'y',
        );

        $this->assertSame('x', $out['select']);
        $this->assertSame('y', $out['scroll_id']);
    }

    private function cargoEntity(int $id_cargo, string $tipo, string $nombreCargo): Cargo
    {
        $c = new Cargo();
        $c->setId_cargo($id_cargo);
        $c->setCargo($nombreCargo);
        $c->setTipoCargoVo($tipo);

        return $c;
    }

    /** @param array<int, Cargo> $byId */
    private function cargoRepoStub(array $byId): CargoRepositoryInterface
    {
        $stub = $this->createStub(CargoRepositoryInterface::class);
        $stub->method('findById')->willReturnCallback(static function (int $id) use ($byId) {
            return $byId[$id] ?? null;
        });

        return $stub;
    }

    /** @param array<int, ActividadAll> $byId */
    private function actividadRepoStub(array $byId): ActividadAllRepositoryInterface
    {
        $stub = $this->createStub(ActividadAllRepositoryInterface::class);
        $stub->method('findById')->willReturnCallback(static function (int $id) use ($byId) {
            return $byId[$id] ?? null;
        });

        return $stub;
    }

    private function actividadStub(string $nom, int $idTipoActiv): ActividadAll
    {
        $stub = $this->createStub(ActividadAll::class);
        $stub->method('getNom_activ')->willReturn($nom);
        $stub->method('getId_tipo_activ')->willReturn($idTipoActiv);

        return $stub;
    }

    private function actividadCargoFila(
        int $id_item,
        int $id_activ,
        int $id_cargo,
        bool $puedeAgd,
        ?string $observ,
    ): object {
        return new class($id_item, $id_activ, $id_cargo, $puedeAgd, $observ) {
            public function __construct(
                private readonly int $id_item,
                private readonly int $id_activ,
                private readonly int $id_cargo,
                private readonly bool $puede_agd,
                private readonly ?string $observ,
            ) {}

            public function getId_item(): int
            {
                return $this->id_item;
            }
            public function getId_activ(): int
            {
                return $this->id_activ;
            }
            public function getId_cargo(): int
            {
                return $this->id_cargo;
            }
            public function isPuede_agd(): bool
            {
                return $this->puede_agd;
            }
            public function getObserv(): ?string
            {
                return $this->observ;
            }
        };
    }
}
