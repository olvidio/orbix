<?php

namespace Tests\integration\utils_database\infrastructure\persistence\postgresql;

use src\utils_database\domain\contracts\MapIdRepositoryInterface;
use src\utils_database\domain\entity\MapId;
use Tests\myTest;
use Tests\factories\utils_database\MapIdFactory;

class PgMapIdRepositoryTest extends myTest
{
    private MapIdRepositoryInterface $repository;
    private MapIdFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(MapIdRepositoryInterface::class);
        $this->factory = new MapIdFactory();
    }

    public function test_guardar_nuevo_map_id()
    {
        $oMapId = $this->factory->createSimple();
        $objeto = $oMapId->getObjeto();
        $idResto = $oMapId->getId_resto();

        $result = $this->repository->Guardar($oMapId);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($objeto, $idResto);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($objeto, $oGuardado->getObjeto());
        $this->assertEquals($idResto, $oGuardado->getId_resto());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_map_id_existente()
    {
        $oMapId = $this->factory->createSimple();
        $objeto = $oMapId->getObjeto();
        $idResto = $oMapId->getId_resto();
        $this->repository->Guardar($oMapId);

        $oActualizado = $this->factory->createSimple($objeto, $idResto);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($objeto, $idResto);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oMapId = $this->factory->createSimple();
        $objeto = $oMapId->getObjeto();
        $idResto = $oMapId->getId_resto();
        $this->repository->Guardar($oMapId);

        $oEncontrado = $this->repository->findById($objeto, $idResto);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(MapId::class, $oEncontrado);
        $this->assertEquals($objeto, $oEncontrado->getObjeto());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oMapId = $this->repository->findById('xyz_inexistente', 999999);
        $this->assertNull($oMapId);
    }

    public function test_eliminar_map_id()
    {
        $oMapId = $this->factory->createSimple();
        $objeto = $oMapId->getObjeto();
        $idResto = $oMapId->getId_resto();
        $this->repository->Guardar($oMapId);

        $oGuardado = $this->repository->findById($objeto, $idResto);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($objeto, $idResto);
        $this->assertNull($oEliminado);
    }
}
