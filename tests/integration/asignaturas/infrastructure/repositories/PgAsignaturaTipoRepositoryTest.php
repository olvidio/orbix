<?php

namespace Tests\integration\asignaturas\infrastructure\persistence\postgresql;

use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\entity\AsignaturaTipo;
use Tests\myTest;
use Tests\factories\asignaturas\AsignaturaTipoFactory;

class PgAsignaturaTipoRepositoryTest extends myTest
{
    private AsignaturaTipoRepositoryInterface $repository;
    private AsignaturaTipoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AsignaturaTipoRepositoryInterface::class);
        $this->factory = new AsignaturaTipoFactory();
    }

    public function test_guardar_nuevo_asignatura_tipo()
    {
        $oAsignaturaTipo = $this->factory->createSimple();
        $id = $oAsignaturaTipo->getId_tipo();

        $result = $this->repository->Guardar($oAsignaturaTipo);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($id, $oGuardado->getId_tipo());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_asignatura_tipo_existente()
    {
        $oAsignaturaTipo = $this->factory->createSimple();
        $id = $oAsignaturaTipo->getId_tipo();
        $this->repository->Guardar($oAsignaturaTipo);

        $oActualizado = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($id);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oAsignaturaTipo = $this->factory->createSimple();
        $id = $oAsignaturaTipo->getId_tipo();
        $this->repository->Guardar($oAsignaturaTipo);

        $oEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(AsignaturaTipo::class, $oEncontrado);
        $this->assertEquals($id, $oEncontrado->getId_tipo());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oAsignaturaTipo = $this->repository->findById(999999);
        $this->assertNull($oAsignaturaTipo);
    }

    public function test_eliminar_asignatura_tipo()
    {
        $oAsignaturaTipo = $this->factory->createSimple();
        $id = $oAsignaturaTipo->getId_tipo();
        $this->repository->Guardar($oAsignaturaTipo);

        $oGuardado = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($id);
        $this->assertNull($oEliminado);
    }
}
