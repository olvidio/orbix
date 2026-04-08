<?php

namespace Tests\integration\asignaturas\infrastructure\persistence\postgresql;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use Tests\myTest;
use Tests\factories\asignaturas\AsignaturaFactory;

class PgAsignaturaRepositoryTest extends myTest
{
    private AsignaturaRepositoryInterface $repository;
    private AsignaturaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $this->factory = new AsignaturaFactory();
    }

    public function test_guardar_nueva_asignatura()
    {
        $oAsignatura = $this->factory->createSimple();
        $id = $oAsignatura->getId_asignatura();

        $result = $this->repository->Guardar($oAsignatura);
        $this->assertTrue($result);

        $oGuardada = $this->repository->findById($id);
        $this->assertNotNull($oGuardada);
        $this->assertEquals($id, $oGuardada->getId_asignatura());

        $this->repository->Eliminar($oGuardada);
    }

    public function test_actualizar_asignatura_existente()
    {
        $oAsignatura = $this->factory->createSimple();
        $id = $oAsignatura->getId_asignatura();
        $this->repository->Guardar($oAsignatura);

        $oActualizada = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizada);
        $this->assertTrue($result);

        $oObtenida = $this->repository->findById($id);
        $this->assertNotNull($oObtenida);

        $this->repository->Eliminar($oObtenida);
    }

    public function test_find_by_id_existente()
    {
        $oAsignatura = $this->factory->createSimple();
        $id = $oAsignatura->getId_asignatura();
        $this->repository->Guardar($oAsignatura);

        $oEncontrada = $this->repository->findById($id);
        $this->assertNotNull($oEncontrada);
        $this->assertInstanceOf(Asignatura::class, $oEncontrada);
        $this->assertEquals($id, $oEncontrada->getId_asignatura());

        $this->repository->Eliminar($oEncontrada);
    }

    public function test_find_by_id_no_existente()
    {
        $oAsignatura = $this->repository->findById(999999);
        $this->assertNull($oAsignatura);
    }

    public function test_eliminar_asignatura()
    {
        $oAsignatura = $this->factory->createSimple();
        $id = $oAsignatura->getId_asignatura();
        $this->repository->Guardar($oAsignatura);

        $oGuardada = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardada);
        $this->assertTrue($result);

        $oEliminada = $this->repository->findById($id);
        $this->assertNull($oEliminada);
    }
}
