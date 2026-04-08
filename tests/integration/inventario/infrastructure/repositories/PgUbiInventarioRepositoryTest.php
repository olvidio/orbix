<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\UbiInventario;
use Tests\myTest;
use Tests\factories\inventario\UbiInventarioFactory;

class PgUbiInventarioRepositoryTest extends myTest
{
    private UbiInventarioRepositoryInterface $repository;
    private UbiInventarioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(UbiInventarioRepositoryInterface::class);
        $this->factory = new UbiInventarioFactory();
    }

    public function test_guardar_nuevo_ubi_inventario()
    {
        $oUbi = $this->factory->createSimple();
        $id = $oUbi->getId_ubi();

        $result = $this->repository->Guardar($oUbi);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($id, $oGuardado->getId_ubi());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_ubi_inventario_existente()
    {
        $oUbi = $this->factory->createSimple();
        $id = $oUbi->getId_ubi();
        $this->repository->Guardar($oUbi);

        $oActualizado = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($id);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oUbi = $this->factory->createSimple();
        $id = $oUbi->getId_ubi();
        $this->repository->Guardar($oUbi);

        $oEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(UbiInventario::class, $oEncontrado);
        $this->assertEquals($id, $oEncontrado->getId_ubi());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oUbi = $this->repository->findById(999999);
        $this->assertNull($oUbi);
    }

    public function test_eliminar_ubi_inventario()
    {
        $oUbi = $this->factory->createSimple();
        $id = $oUbi->getId_ubi();
        $this->repository->Guardar($oUbi);

        $oGuardado = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($id);
        $this->assertNull($oEliminado);
    }
}
