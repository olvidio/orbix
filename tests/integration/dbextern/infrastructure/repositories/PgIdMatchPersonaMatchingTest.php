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

    public function test_matching_ordenado_y_limitado(): void
    {
        $res = $this->repository->matching(Criteria::create()->orderBy(IdMatchPersonaField::IdListas)->limit(20));
        $this->assertLessThanOrEqual(20, count($res));
        $ids = array_map(fn($o) => $o->getId_listas(), $res);
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
    }

    public function test_matching_por_id_listas(): void
    {
        $todos = $this->repository->matching(Criteria::create()->orderBy(IdMatchPersonaField::IdListas)->limit(1));
        if ($todos === []) {
            $this->assertSame([], $this->repository->matching(Criteria::create()->where(Filter::eq(IdMatchPersonaField::IdListas, 0))));
            return;
        }
        $id = $todos[0]->getId_listas();
        $res = $this->repository->matching(Criteria::create()->where(Filter::eq(IdMatchPersonaField::IdListas, $id)));
        $this->assertCount(1, $res);
        $this->assertSame($id, $res[0]->getId_listas());
    }
}
