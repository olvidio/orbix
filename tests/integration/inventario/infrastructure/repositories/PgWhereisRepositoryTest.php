<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\WhereisRepositoryInterface;
use Tests\myTest;

class PgWhereisRepositoryTest extends myTest
{
    private WhereisRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(WhereisRepositoryInterface::class);
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999981));
    }

    public function test_get_new_id()
    {
        $nid = $this->repository->getNewId();
        $this->assertTrue(is_numeric($nid));
    }
}
