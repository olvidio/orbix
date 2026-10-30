<?php

namespace Tests\integration\actividadcargos\infrastructure\repositories;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\criteria\ActividadCargoField;
use src\actividadcargos\domain\criteria\CargoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\actividadcargos\ActividadCargoFactory;
use Tests\myTest;

/**
 * `matching()` de PgActividadCargoDlRepository y PgCargoRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgActividadCargosMatchingTest extends myTest
{
    private ActividadCargoRepositoryInterface $actividadCargoRepo;
    private CargoRepositoryInterface $cargoRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->actividadCargoRepo = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $this->cargoRepo = $GLOBALS['container']->get(CargoRepositoryInterface::class);
    }

    public function test_actividad_cargo_matching_por_item_equivale_a_legacy(): void
    {
        $oActividadCargo = (new ActividadCargoFactory())->createSimple();
        $idItem = $oActividadCargo->getId_item();
        $this->actividadCargoRepo->Guardar($oActividadCargo);

        $nuevo = $this->actividadCargoRepo->matching(
            Criteria::create()
                ->where(Filter::eq(ActividadCargoField::IdItem, $idItem))
                ->andWhere(Filter::eq(ActividadCargoField::IdActiv, $oActividadCargo->getId_activ()))
        );

        $this->assertCount(1, $nuevo);
        $this->assertSame($idItem, $nuevo[0]->getId_item());
        $this->assertSame($oActividadCargo->getId_activ(), $nuevo[0]->getId_activ());

        $this->actividadCargoRepo->Eliminar($nuevo[0]);
        $this->assertSame([], $this->actividadCargoRepo->matching(
            Criteria::create()->where(Filter::eq(ActividadCargoField::IdItem, $idItem))
        ));
    }

    public function test_cargo_matching_por_tipo_con_orden_equivale_a_legacy(): void
    {
        $nuevo = $this->cargoRepo->matching(
            Criteria::create()
                ->where(Filter::eq(CargoField::TipoCargo, 'sacd'))
                ->orderBy(CargoField::OrdenCargo)
        );

        $this->assertNotEmpty($nuevo);
        foreach ($nuevo as $cargo) {
            $this->assertSame('sacd', $cargo->getTipo_cargo());
        }
        $ordenes = array_map(static fn($c) => $c->getOrden_cargo() ?? PHP_INT_MAX, $nuevo);
        $esperado = $ordenes;
        sort($esperado);
        $this->assertSame($esperado, $ordenes);
    }

    public function test_cargo_matching_sin_acentos_equivale_a_legacy(): void
    {
        $nuevo = $this->cargoRepo->matching(
            Criteria::create()->where(Filter::matchesUnaccented(CargoField::Cargo, 'd.est'))
        );

        $this->assertIsArray($nuevo);
        foreach ($nuevo as $cargo) {
            $this->assertStringContainsStringIgnoringCase('d.est', $cargo->getCargo());
        }
    }
}
