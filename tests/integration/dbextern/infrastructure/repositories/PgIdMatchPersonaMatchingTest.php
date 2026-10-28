<?php

namespace Tests\integration\dbextern\infrastructure\repositories;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\criteria\IdMatchPersonaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;

/**
 * `matching()` de PgIdMatchPersonaRepository (paso 4.3 de docs/dev/refactor_busquedas_criteria.md).
 * Compara con el método legacy sobre los datos existentes.
 */
class PgIdMatchPersonaMatchingTest extends myTest
{
    private IdMatchPersonaRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
    }

    public function test_matching_ordenado_equivale_a_legacy(): void
    {
        $this->assertEquals(
            $this->repository->getIdMatchPersonas(['_ordre' => 'id_listas', '_limit' => 20]),
            $this->repository->matching(Criteria::create()->orderBy(IdMatchPersonaField::IdListas)->limit(20))
        );
    }

    public function test_matching_por_id_listas_equivale_a_legacy(): void
    {
        $todos = $this->repository->matching(Criteria::create()->orderBy(IdMatchPersonaField::IdListas)->limit(1));
        if ($todos === []) {
            $this->assertSame([], $this->repository->getIdMatchPersonas(['id_listas' => 0]));
            return;
        }
        $id = $todos[0]->getId_listas();
        $this->assertEquals(
            $this->repository->getIdMatchPersonas(['id_listas' => $id]),
            $this->repository->matching(Criteria::create()->where(Filter::eq(IdMatchPersonaField::IdListas, $id)))
        );
    }
}
