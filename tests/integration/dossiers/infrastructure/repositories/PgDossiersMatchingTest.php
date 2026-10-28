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
        $legacy = $this->tipoRepo->getTiposDossiers(['tabla_from' => 'p', '_ordre' => 'id_tipo_dossier']);

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);
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
        $legacy = $this->dossierRepo->getDossieres([
            'tabla' => $dossier->getTablaVo()->value(),
            'id_pau' => $dossier->getId_pau(),
            'id_tipo_dossier' => $dossier->getId_tipo_dossier(),
        ]);

        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $this->dossierRepo->Eliminar($nuevo[0]);
        $this->assertSame([], $this->dossierRepo->matching($criteria));
    }
}
