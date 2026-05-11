<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\SelectCargosDeActividadTableData;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\Cargo;

final class SelectCargosDeActividadTableDataTest extends TestCase
{
    public function test_fila_con_permiso_escritura_incluye_sel_formateada(): void
    {
        $fila = $this->actividadCargoFila(1, 10, 200, 5, false, null);
        $cargo = $this->cargoEntity(5, 'd', 'Director');
        $repo = $this->cargoRepoStub([5 => $cargo]);
        $persona = $this->personaMock('Apellido, Nombre', 'DL1', 'n');

        $out = SelectCargosDeActividadTableData::buildValorRows(
            2,
            1,
            [$fila],
            $repo,
            static fn (?int $id) => $id === 200 ? $persona : null,
            ['n' => ['perm' => true, 'obj' => 'o', 'nom' => 'nom']],
            null,
            null,
        );

        $this->assertSame('', $out['msg_err']);
        $this->assertSame([
            1 => [
                'sel' => '200#10#2#1',
                1 => 'Director',
                2 => 'Apellido, Nombre  (DL1)',
                3 => 'no',
                4 => '',
            ],
        ], $out['a_valores']);
    }

    public function test_sin_permiso_deja_sel_vacio(): void
    {
        $fila = $this->actividadCargoFila(1, 11, 201, 3, false, null);
        $repo = $this->cargoRepoStub([3 => $this->cargoEntity(3, 'd', 'Aux')]);
        $persona = $this->personaMock('X', 'Y', 'n');

        $out = SelectCargosDeActividadTableData::buildValorRows(
            1,
            1,
            [$fila],
            $repo,
            static fn () => $persona,
            ['n' => ['perm' => false]],
            null,
            null,
        );

        $this->assertSame([
            1 => [
                'sel' => '',
                1 => 'Aux',
                2 => 'X  (Y)',
                3 => 'no',
                4 => '',
            ],
        ], $out['a_valores']);
    }

    public function test_omite_cargo_sacd_en_sf(): void
    {
        $fila = $this->actividadCargoFila(1, 12, 202, 9, false, null);
        $repo = $this->cargoRepoStub([9 => $this->cargoEntity(9, 'sacd', 'S')]);

        $findCalled = false;
        $out = SelectCargosDeActividadTableData::buildValorRows(
            1,
            2,
            [$fila],
            $repo,
            function (?int $_id) use (&$findCalled) {
                $findCalled = true;

                return null;
            },
            [],
            null,
            null,
        );

        $this->assertFalse($findCalled, 'No debe resolverse persona si la fila sacd se omite en sf.');
        $this->assertSame([], $out['a_valores']);
    }

    public function test_persona_ausente_acumula_msg_err(): void
    {
        $fila = $this->actividadCargoFila(1, 13, 203, 4, false, null);
        $repo = $this->cargoRepoStub([4 => $this->cargoEntity(4, 'd', 'C')]);

        $out = SelectCargosDeActividadTableData::buildValorRows(
            1,
            1,
            [$fila],
            $repo,
            static fn () => null,
            [],
            null,
            null,
        );

        $this->assertStringContainsString('No encuentro a nadie con id_nom: 203', $out['msg_err']);
        $this->assertSame([], $out['a_valores']);
    }

    public function test_anade_select_y_scroll_cuando_vienen(): void
    {
        $fila = $this->actividadCargoFila(1, 14, 204, 6, false, null);
        $repo = $this->cargoRepoStub([6 => $this->cargoEntity(6, 'd', 'R')]);
        $persona = $this->personaMock('P', 'C', null);

        $out = SelectCargosDeActividadTableData::buildValorRows(
            1,
            1,
            [$fila],
            $repo,
            static fn () => $persona,
            [],
            'sel1',
            'sc99',
        );

        $this->assertArrayHasKey('select', $out['a_valores']);
        $this->assertSame('sel1', $out['a_valores']['select']);
        $this->assertSame('sc99', $out['a_valores']['scroll_id']);
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

    private function actividadCargoFila(
        int $id_schema,
        int $id_item,
        int $id_nom,
        int $id_cargo,
        bool $puedeAgd,
        ?string $observ,
    ): object {
        return new class($id_schema, $id_item, $id_nom, $id_cargo, $puedeAgd, $observ) {
            public function __construct(
                private readonly int $id_schema,
                private readonly int $id_item,
                private readonly int $id_nom,
                private readonly int $id_cargo,
                private readonly bool $puede_agd,
                private readonly ?string $observ,
            ) {}

            public function getId_schema(): int
            {
                return $this->id_schema;
            }
            public function getId_item(): int
            {
                return $this->id_item;
            }
            public function getId_nom(): ?int
            {
                return $this->id_nom;
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

    private function personaMock(string $nom, string $ctr, ?string $idTabla): object
    {
        return new class($nom, $ctr, $idTabla) {
            public function __construct(
                private readonly string $nom,
                private readonly string $ctr,
                private readonly ?string $id_tabla,
            ) {}

            public function getPrefApellidosNombre(): string
            {
                return $this->nom;
            }
            public function getCentro_o_dl(): string
            {
                return $this->ctr;
            }
            public function getId_tabla(): ?string
            {
                return $this->id_tabla;
            }
        };
    }
}
