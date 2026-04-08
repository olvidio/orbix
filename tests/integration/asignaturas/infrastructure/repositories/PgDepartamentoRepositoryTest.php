<?php

namespace Tests\integration\asignaturas\infrastructure\persistence\postgresql;

use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Departamento;
use Tests\myTest;
use Tests\factories\asignaturas\DepartamentoFactory;

class PgDepartamentoRepositoryTest extends myTest
{
    private DepartamentoRepositoryInterface $repository;
    private DepartamentoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DepartamentoRepositoryInterface::class);
        $this->factory = new DepartamentoFactory();
    }

    public function test_guardar_nuevo_departamento()
    {
        $oDepartamento = $this->factory->createSimple();
        $id = $oDepartamento->getId_departamento();

        $result = $this->repository->Guardar($oDepartamento);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($id, $oGuardado->getId_departamento());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_departamento_existente()
    {
        $oDepartamento = $this->factory->createSimple();
        $id = $oDepartamento->getId_departamento();
        $this->repository->Guardar($oDepartamento);

        $oActualizado = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($id);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oDepartamento = $this->factory->createSimple();
        $id = $oDepartamento->getId_departamento();
        $this->repository->Guardar($oDepartamento);

        $oEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(Departamento::class, $oEncontrado);
        $this->assertEquals($id, $oEncontrado->getId_departamento());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oDepartamento = $this->repository->findById(999999);
        $this->assertNull($oDepartamento);
    }

    public function test_eliminar_departamento()
    {
        $oDepartamento = $this->factory->createSimple();
        $id = $oDepartamento->getId_departamento();
        $this->repository->Guardar($oDepartamento);

        $oGuardado = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($id);
        $this->assertNull($oEliminado);
    }
}
