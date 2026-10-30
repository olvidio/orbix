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

    public function test_tipo_tarifa_matching_ordenado(): void
    {
        $nuevo = $this->tipoTarifaRepo->matching(
            Criteria::create()->orderBy(TipoTarifaField::Sfsv)->addOrderBy(TipoTarifaField::Letra)
        );

        $sfsv = array_map(fn ($o) => $o->getSfsv(), $nuevo);
        $ordenado = $sfsv;
        sort($ordenado);
        $this->assertSame($ordenado, $sfsv);
    }

    public function test_tipo_tarifa_matching_por_sfsv(): void
    {
        $nuevo = $this->tipoTarifaRepo->matching(Criteria::create()->where(Filter::eq(TipoTarifaField::Sfsv, 1)));

        foreach ($nuevo as $oTipoTarifa) {
            $this->assertSame(1, $oTipoTarifa->getSfsv());
        }
    }

    public function test_relacion_matching_por_item(): void
    {
        $oRelacion = (new RelacionTarifaTipoActividadFactory())->createSimple();
        $idItem = $oRelacion->getId_item();
        $this->relacionRepo->Guardar($oRelacion);

        $criteria = Criteria::create()->where(Filter::eq(RelacionTarifaTipoActividadField::IdItem, $idItem));
        $nuevo = $this->relacionRepo->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertSame($idItem, $nuevo[0]->getId_item());

        $this->relacionRepo->Eliminar($nuevo[0]);
        $this->assertSame([], $this->relacionRepo->matching($criteria));
    }
}
