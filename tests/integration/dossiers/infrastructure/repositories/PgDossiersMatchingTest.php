<?php

namespace Tests\integration\dossiers\infrastructure\repositories;

use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\criteria\DossierField;
use src\dossiers\domain\criteria\TipoDossierField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\dossiers\DossierFactory;
use Tests\myTest;

/**
 * `matching()` de PgTipoDossierRepository y PgDossierRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgDossiersMatchingTest extends myTest
{
    private TipoDossierRepositoryInterface $tipoRepo;
    private DossierRepositoryInterface $dossierRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->tipoRepo = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $this->dossierRepo = $GLOBALS['container']->get(DossierRepositoryInterface::class);
    }

    public function test_tipo_dossier_matching_por_tabla_from_con_orden_equivale_a_legacy(): void
    {
        $nuevo = $this->tipoRepo->matching(
            Criteria::create()
                ->where(Filter::eq(TipoDossierField::TablaFrom, 'p'))
                ->orderBy(TipoDossierField::IdTipoDossier)
        );

        $this->assertNotEmpty($nuevo);
        $ids = array_map(fn($t) => $t->getId_tipo_dossier(), $nuevo);
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
        foreach ($nuevo as $tipo) {
            $this->assertSame('p', $tipo->getTabla_from());
        }
    }

    public function test_tipo_dossier_find_by_codigo_inexistente_devuelve_null(): void
    {
        $this->assertNull($this->tipoRepo->findByCodigo('codigo-que-no-existe-xyz'));
    }

    public function test_dossier_matching_por_pk_equivale_a_legacy(): void
    {
        $dossier = (new DossierFactory())->createSimple();
        $this->dossierRepo->Guardar($dossier);

        $criteria = Criteria::create()
            ->where(Filter::eq(DossierField::Tabla, $dossier->getTablaVo()->value()))
            ->andWhere(Filter::eq(DossierField::IdPau, $dossier->getId_pau()))
            ->andWhere(Filter::eq(DossierField::IdTipoDossier, $dossier->getId_tipo_dossier()));
        $nuevo = $this->dossierRepo->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertEquals($dossier->getId_pau(), $nuevo[0]->getId_pau());
        $this->assertEquals($dossier->getId_tipo_dossier(), $nuevo[0]->getId_tipo_dossier());

        $this->dossierRepo->Eliminar($nuevo[0]);
        $this->assertSame([], $this->dossierRepo->matching($criteria));
    }
}
