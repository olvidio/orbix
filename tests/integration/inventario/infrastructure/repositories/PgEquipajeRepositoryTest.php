<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;
use Tests\myTest;
use Tests\factories\inventario\EquipajeFactory;

class PgEquipajeRepositoryTest extends myTest
{
    private EquipajeRepositoryInterface $repository;
    private EquipajeFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
        $this->factory = new EquipajeFactory();
    }

    public function test_guardar_nuevo_equipaje()
    {
        $oEquipaje = $this->factory->createSimple();
        $id = $oEquipaje->getId_equipaje();

        $result = $this->repository->Guardar($oEquipaje);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($id, $oGuardado->getId_equipaje());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_equipaje_existente()
    {
        $oEquipaje = $this->factory->createSimple();
        $id = $oEquipaje->getId_equipaje();
        $this->repository->Guardar($oEquipaje);

        $oActualizado = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($id);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oEquipaje = $this->factory->createSimple();
        $id = $oEquipaje->getId_equipaje();
        $this->repository->Guardar($oEquipaje);

        $oEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(Equipaje::class, $oEncontrado);
        $this->assertEquals($id, $oEncontrado->getId_equipaje());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oEquipaje = $this->repository->findById(999999);
        $this->assertNull($oEquipaje);
    }

    public function test_eliminar_equipaje()
    {
        $oEquipaje = $this->factory->createSimple();
        $id = $oEquipaje->getId_equipaje();
        $this->repository->Guardar($oEquipaje);

        $oGuardado = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($id);
        $this->assertNull($oEliminado);
    }
}
