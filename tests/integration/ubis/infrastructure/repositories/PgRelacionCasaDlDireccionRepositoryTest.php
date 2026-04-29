<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use Tests\myTest;

class PgRelacionCasaDlDireccionRepositoryTest extends myTest
{
    private RelacionCasaDlDireccionRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(RelacionCasaDlDireccionRepositoryInterface::class);
    }

    public function test_existe_relacion_falso_para_ids_inexistentes()
    {
        $this->assertFalse($this->repository->existeRelacion(999999991, 999999992));
    }

    public function test_get_relaciones_por_ubi_devuelve_array()
    {
        $this->assertIsArray($this->repository->getRelacionesPorUbi(999999981));
    }
}
