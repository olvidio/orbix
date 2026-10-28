<?php

namespace Tests\integration\actividadtarifas\infrastructure\repositories;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\criteria\RelacionTarifaTipoActividadField;
use src\actividadtarifas\domain\criteria\TipoTarifaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\actividadtarifas\RelacionTarifaTipoActividadFactory;
use Tests\myTest;

/**
 * `matching()` de PgTipoTarifaRepository y PgRelacionTarifaTipoActividadRepository
 * (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgActividadTarifasMatchingTest extends myTest
{
    private TipoTarifaRepositoryInterface $tipoTarifaRepo;
    private RelacionTarifaTipoActividadRepositoryInterface $relacionRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->tipoTarifaRepo = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $this->relacionRepo = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
    }

    public function test_tipo_tarifa_matching_ordenado_equivale_a_legacy(): void
    {
        $nuevo = $this->tipoTarifaRepo->matching(
            Criteria::create()->orderBy(TipoTarifaField::Sfsv)->addOrderBy(TipoTarifaField::Letra)
        );
        $legacy = $this->tipoTarifaRepo->getTipoTarifas(['_ordre' => 'sfsv,letra']);

        $this->assertEquals($legacy, $nuevo);
    }

    public function test_tipo_tarifa_matching_por_sfsv_equivale_a_legacy(): void
    {
        $nuevo = $this->tipoTarifaRepo->matching(Criteria::create()->where(Filter::eq(TipoTarifaField::Sfsv, 1)));
        $legacy = $this->tipoTarifaRepo->getTipoTarifas(['sfsv' => 1]);

        $this->assertEquals($legacy, $nuevo);
    }

    public function test_relacion_matching_por_item_equivale_a_legacy(): void
    {
        $oRelacion = (new RelacionTarifaTipoActividadFactory())->createSimple();
        $idItem = $oRelacion->getId_item();
        $this->relacionRepo->Guardar($oRelacion);

        $criteria = Criteria::create()->where(Filter::eq(RelacionTarifaTipoActividadField::IdItem, $idItem));
        $nuevo = $this->relacionRepo->matching($criteria);
        $legacy = $this->relacionRepo->getTipoActivTarifas(['id_item' => $idItem]);

        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $this->relacionRepo->Eliminar($nuevo[0]);
        $this->assertSame([], $this->relacionRepo->matching($criteria));
    }
}
