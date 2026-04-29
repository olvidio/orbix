<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use Tests\myTest;

class PgRelacionCentroDireccionRepositoryTest extends myTest
{
    private RelacionCentroDireccionRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(RelacionCentroDireccionRepositoryInterface::class);
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
