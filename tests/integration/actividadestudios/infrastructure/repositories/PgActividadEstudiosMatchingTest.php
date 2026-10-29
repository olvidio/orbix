<?php

namespace Tests\integration\actividadestudios\infrastructure\persistence\postgresql;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\criteria\ActividadAsignaturaField;
use src\actividadestudios\domain\criteria\MatriculaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\actividadestudios\ActividadAsignaturaFactory;
use Tests\factories\actividadestudios\MatriculaFactory;
use Tests\myTest;

/**
 * `matching()` de PgMatriculaRepository y PgActividadAsignaturaRepository (vía los repos Dl)
 * (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgActividadEstudiosMatchingTest extends myTest
{
    private MatriculaDlRepositoryInterface $matriculaRepo;
    private ActividadAsignaturaDlRepositoryInterface $asignaturaRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->matriculaRepo = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
        $this->asignaturaRepo = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
    }

    public function test_matricula_matching_equivale_a_legacy(): void
    {
        $o = (new MatriculaFactory())->createSimple();
        $this->matriculaRepo->Guardar($o);
        $idActiv = $o->getId_activ();
        $idNom = $o->getId_nom();

        $criteria = Criteria::create()
            ->where(Filter::eq(MatriculaField::IdNom, $idNom))
            ->andWhere(Filter::eq(MatriculaField::IdActiv, $idActiv))
            ->orderBy(MatriculaField::IdNivel);
        $nuevo = $this->matriculaRepo->matching($criteria);
        $legacy = $this->matriculaRepo->getMatriculas(
            ['id_nom' => $idNom, 'id_activ' => $idActiv, '_ordre' => 'id_nivel']
        );

        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $this->matriculaRepo->Eliminar($o);
    }

    public function test_matricula_matching_in_equivale_a_legacy(): void
    {
        $o = (new MatriculaFactory())->createSimple();
        $this->matriculaRepo->Guardar($o);
        $idActiv = $o->getId_activ();

        $nuevo = $this->matriculaRepo->matching(
            Criteria::create()->where(Filter::in(MatriculaField::IdActiv, [$idActiv, $idActiv + 1]))
        );
        $legacy = $this->matriculaRepo->getMatriculas(
            ['id_activ' => '{' . $idActiv . ', ' . ($idActiv + 1) . '}'],
            ['id_activ' => 'ANY']
        );

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);

        $this->matriculaRepo->Eliminar($o);
    }

    public function test_actividad_asignatura_matching_is_null_equivale_a_legacy(): void
    {
        $o = (new ActividadAsignaturaFactory())->createSimple();
        $this->asignaturaRepo->Guardar($o);
        $idActiv = $o->getId_activ();

        $nuevo = $this->asignaturaRepo->matching(
            Criteria::create()
                ->where(Filter::eq(ActividadAsignaturaField::IdActiv, $idActiv))
                ->andWhere(Filter::isNull(ActividadAsignaturaField::Tipo))
                ->orderBy(ActividadAsignaturaField::IdAsignatura)
        );
        $legacy = $this->asignaturaRepo->getActividadAsignaturas(
            ['id_activ' => $idActiv, 'tipo' => 'x', '_ordre' => 'id_asignatura'],
            ['tipo' => 'IS NULL']
        );

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);

        $this->asignaturaRepo->Eliminar($o);
    }
}
