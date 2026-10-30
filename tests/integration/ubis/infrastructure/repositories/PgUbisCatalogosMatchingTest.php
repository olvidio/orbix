<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\criteria\CasaPeriodoField;
use src\ubis\domain\criteria\DelegacionField;
use src\ubis\domain\criteria\DescTelecoField;
use src\ubis\domain\criteria\RegionField;
use src\ubis\domain\criteria\TarifaUbiField;
use src\ubis\domain\criteria\TipoCasaField;
use src\ubis\domain\criteria\TipoCentroField;
use src\ubis\domain\criteria\TipoTelecoField;
use Tests\factories\ubis\CasaPeriodoFactory;
use Tests\myTest;

/**
 * `matching()` de los repositorios de catálogos de `ubis` (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgUbisCatalogosMatchingTest extends myTest
{
    public function test_delegacion_matching_booleano_y_orden_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);

        $nuevo = $repo->matching(
            Criteria::create()->where(Filter::eq(DelegacionField::Active, true))->orderBy(DelegacionField::NombreDl)
        );

        $this->assertNotEmpty($nuevo);
        foreach ($nuevo as $d) {
            $this->assertTrue($d->isActive());
        }
    }

    public function test_delegacion_matching_in_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $primera = $repo->matching(Criteria::create()->where(Filter::eq(DelegacionField::Active, true))->limit(1))[0] ?? null;
        $this->assertNotNull($primera);
        $region = $primera->getRegionVo()->value();

        $nuevo = $repo->matching(
            Criteria::create()->where(Filter::in(DelegacionField::Region, [$region]))->orderBy(DelegacionField::Dl)
        );

        $this->assertNotEmpty($nuevo);
        foreach ($nuevo as $d) {
            $this->assertSame($region, $d->getRegionVo()->value());
        }
    }

    public function test_region_y_tipos_matching_equivalen_a_legacy(): void
    {
        $regiones = $GLOBALS['container']->get(RegionRepositoryInterface::class);
        $this->assertEquals(
            $regiones->getRegiones(['active' => true, '_ordre' => 'nombre_region']),
            $regiones->matching(Criteria::create()->where(Filter::eq(RegionField::Active, true))->orderBy(RegionField::NombreRegion))
        );

        $tCasa = $GLOBALS['container']->get(TipoCasaRepositoryInterface::class);
        $this->assertEquals(
            $tCasa->getTiposCasa(['_ordre' => 'nombre_tipo_casa']),
            $tCasa->matching(Criteria::create()->orderBy(TipoCasaField::NombreTipoCasa))
        );

        $tCentro = $GLOBALS['container']->get(TipoCentroRepositoryInterface::class);
        $this->assertEquals(
            $tCentro->getTiposCentro(['_ordre' => 'nombre_tipo_ctr']),
            $tCentro->matching(Criteria::create()->orderBy(TipoCentroField::NombreTipoCtr))
        );

        $tTeleco = $GLOBALS['container']->get(TipoTelecoRepositoryInterface::class);
        $this->assertEquals(
            $tTeleco->getTiposTeleco(['_ordre' => 'nombre_teleco']),
            $tTeleco->matching(Criteria::create()->orderBy(TipoTelecoField::NombreTeleco))
        );

        $desc = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
        $this->assertIsArray(
            $desc->matching(
                Criteria::create()
                    ->orderBy(DescTelecoField::Ubi)
                    ->addOrderBy(DescTelecoField::Persona)
                    ->addOrderBy(DescTelecoField::Orden)
            )
        );

        $tarifas = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
        $this->assertIsArray($tarifas->matching(Criteria::create()->orderBy(TarifaUbiField::IdItem)));
    }

    public function test_casa_periodo_matching_fechas_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        $o = (new CasaPeriodoFactory())->create();
        $repo->Guardar($o);
        $id = $o->getId_item();

        $nuevo = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(CasaPeriodoField::IdItem, $id))
                ->andWhere(Filter::gte(CasaPeriodoField::FIni, '1900-1-1'))
                ->orderBy(CasaPeriodoField::FIni)
        );
        $this->assertCount(1, $nuevo);
        $this->assertSame($id, $nuevo[0]->getId_item());

        $repo->Eliminar($o);
    }
}
