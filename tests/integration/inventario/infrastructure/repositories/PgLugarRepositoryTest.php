<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use Tests\myTest;

class PgLugarRepositoryTest extends myTest
{
    private LugarRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
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
