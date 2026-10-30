<?php

namespace Tests\integration\casas\infrastructure\repositories;

use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\criteria\GrupoCasaField;
use src\casas\domain\criteria\IngresoField;
use src\casas\domain\criteria\UbiGastoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use Tests\factories\casas\UbiGastoFactory;
use Tests\factories\ubis\CasaFactory;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `casas` (paso 4.2 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgCasasMatchingTest extends myTest
{
    private CasaDlRepositoryInterface $casaRepository;
    private $casa;

    public function setUp(): void
    {
        parent::setUp();
        $this->casaRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $this->casa = (new CasaFactory())->createSimple(-10019001);
        $this->casaRepository->Guardar($this->casa);
    }

    public function tearDown(): void
    {
        $this->casaRepository->Eliminar($this->casa);
        parent::tearDown();
    }

    public function test_gastos_entre_fechas(): void
    {
        $repo = $GLOBALS['container']->get(UbiGastoRepositoryInterface::class);
        $oGasto = (new UbiGastoFactory())->createSimple();
        $oGasto->setF_gasto(new DateTimeLocal('2031-03-15'));
        $repo->Guardar($oGasto);

        try {
            $criteria = Criteria::create()
                ->where(Filter::eq(UbiGastoField::IdUbi, -10019001))
                ->andWhere(Filter::between(UbiGastoField::FGasto, '2031-01-01', '2031-12-31'))
                ->orderBy(UbiGastoField::FGasto);
            $nuevo = $repo->matching($criteria);

            $this->assertCount(1, $nuevo);
            $this->assertSame('2031-03-15', $nuevo[0]->getF_gasto()->format('Y-m-d'));

            $fuera = Criteria::create()
                ->where(Filter::eq(UbiGastoField::IdUbi, -10019001))
                ->andWhere(Filter::between(UbiGastoField::FGasto, '2032-01-01', '2032-12-31'));
            $this->assertSame([], $repo->matching($fuera));
        } finally {
            $repo->Eliminar($oGasto);
        }
    }

    public function test_grupos_e_ingresos_con_orden(): void
    {
        $grupos = $GLOBALS['container']->get(GrupoCasaRepositoryInterface::class);
        $this->assertIsArray($grupos->matching(Criteria::create()->orderBy(GrupoCasaField::IdItem)));

        $ingresos = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
        $this->assertIsArray($ingresos->matching(Criteria::create()->orderBy(IngresoField::IdActiv)));
    }
}
