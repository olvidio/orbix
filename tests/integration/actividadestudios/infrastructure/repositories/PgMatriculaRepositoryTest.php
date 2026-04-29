<?php

namespace Tests\integration\actividadestudios\infrastructure\persistence\postgresql;

use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use Tests\factories\actividadestudios\MatriculaFactory;
use Tests\myTest;

class PgMatriculaRepositoryTest extends myTest
{
    private MatriculaRepositoryInterface $repository;
    private MatriculaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        $this->factory = new MatriculaFactory();
    }

    public function test_guardar_eliminar_matricula()
    {
        $o = $this->factory->createSimple();
        $ida = $o->getId_activ();
        $idb = $o->getId_nom();
        $idc = $o->getId_asignatura();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($ida, $idc, $idb);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Matricula::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($ida, $idc, $idb));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999991, 999999992, 999999993));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999991, 999999992, 999999993));
    }
}
