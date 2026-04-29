<?php

namespace Tests\integration\actividadestudios\infrastructure\persistence\postgresql;

use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use Tests\factories\actividadestudios\ActividadAsignaturaFactory;
use Tests\myTest;

class PgActividadAsignaturaRepositoryTest extends myTest
{
    private ActividadAsignaturaRepositoryInterface $repository;
    private ActividadAsignaturaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
        $this->factory = new ActividadAsignaturaFactory();
    }

    public function test_guardar_eliminar_actividad_asignatura()
    {
        $o = $this->factory->createSimple();
        $ida = $o->getId_activ();
        $idb = $o->getId_asignatura();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($ida, $idb);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(ActividadAsignatura::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($ida, $idb));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999991, 999999992));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999991, 999999992));
    }
}
