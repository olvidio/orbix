<?php

namespace Tests\integration\actividadplazas\infrastructure\persistence\postgresql;

use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\criteria\ActividadPlazasField;
use src\actividadplazas\domain\criteria\PlazaPeticionField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\actividadplazas\ActividadPlazasFactory;
use Tests\factories\actividadplazas\PlazaPeticionFactory;
use Tests\myTest;

/**
 * `matching()` de PgActividadPlazasRepository y PgPlazaPeticionRepository
 * (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgActividadPlazasMatchingTest extends myTest
{
    private ActividadPlazasRepositoryInterface $plazasRepo;
    private PlazaPeticionRepositoryInterface $peticionRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->plazasRepo = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
        $this->peticionRepo = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
    }

    public function test_plazas_matching_por_id_equivale_a_legacy(): void
    {
        $oPlazas = (new ActividadPlazasFactory())->createSimple();
        $id = $oPlazas->getId_activ();
        $this->plazasRepo->Guardar($oPlazas);

        $nuevo = $this->plazasRepo->matching(
            Criteria::create()
                ->where(Filter::eq(ActividadPlazasField::IdActiv, $id))
                ->andWhere(Filter::eq(ActividadPlazasField::IdDl, 1))
        );

        $this->assertCount(1, $nuevo);
        $this->assertSame($id, $nuevo[0]->getId_activ());

        $this->plazasRepo->Eliminar($oPlazas);
    }

    public function test_peticion_matching_por_nom_y_regex_equivale_a_legacy(): void
    {
        $oPeticion = (new PlazaPeticionFactory())->createSimple();
        $idNom = $oPeticion->getId_nom();
        $this->peticionRepo->Guardar($oPeticion);

        $criteria = Criteria::create()
            ->where(Filter::eq(PlazaPeticionField::IdNom, $idNom))
            ->andWhere(Filter::matches(PlazaPeticionField::IdNom, '^\d{4}9'))
            ->orderBy(PlazaPeticionField::Orden);
        $nuevo = $this->peticionRepo->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertSame($idNom, $nuevo[0]->getId_nom());

        $this->peticionRepo->Eliminar($oPeticion);
    }
}
