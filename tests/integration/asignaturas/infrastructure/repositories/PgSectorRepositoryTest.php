<?php

namespace Tests\integration\asignaturas\infrastructure\persistence\postgresql;

use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\entity\Sector;
use Tests\myTest;
use Tests\factories\asignaturas\SectorFactory;

class PgSectorRepositoryTest extends myTest
{
    private SectorRepositoryInterface $repository;
    private SectorFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(SectorRepositoryInterface::class);
        $this->factory = new SectorFactory();
    }

    public function test_guardar_nuevo_sector()
    {
        $oSector = $this->factory->createSimple();
        $id = $oSector->getId_sector();

        $result = $this->repository->Guardar($oSector);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($id, $oGuardado->getId_sector());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_sector_existente()
    {
        $oSector = $this->factory->createSimple();
        $id = $oSector->getId_sector();
        $this->repository->Guardar($oSector);

        $oActualizado = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($id);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oSector = $this->factory->createSimple();
        $id = $oSector->getId_sector();
        $this->repository->Guardar($oSector);

        $oEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(Sector::class, $oEncontrado);
        $this->assertEquals($id, $oEncontrado->getId_sector());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oSector = $this->repository->findById(999999);
        $this->assertNull($oSector);
    }

    public function test_eliminar_sector()
    {
        $oSector = $this->factory->createSimple();
        $id = $oSector->getId_sector();
        $this->repository->Guardar($oSector);

        $oGuardado = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($id);
        $this->assertNull($oEliminado);
    }
}
