<?php

namespace Tests\integration\shared\infrastructure\persistence\postgresql;

use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\entity\ColaMail;
use Tests\myTest;
use Tests\factories\shared\ColaMailFactory;

class PgColaMailRepositoryTest extends myTest
{
    private ColaMailRepositoryInterface $repository;
    private ColaMailFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ColaMailRepositoryInterface::class);
        $this->factory = new ColaMailFactory();
    }

    public function test_guardar_nueva_cola_mail()
    {
        $oColaMail = $this->factory->createSimple();
        $uuid = $oColaMail->getUuid_item()->value();

        $result = $this->repository->Guardar($oColaMail);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($uuid);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($uuid, $oGuardado->getUuid_item()->value());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_cola_mail_existente()
    {
        $oColaMail = $this->factory->createSimple();
        $uuid = $oColaMail->getUuid_item()->value();
        $this->repository->Guardar($oColaMail);

        $oActualizado = $this->factory->createSimple($uuid);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($uuid);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oColaMail = $this->factory->createSimple();
        $uuid = $oColaMail->getUuid_item()->value();
        $this->repository->Guardar($oColaMail);

        $oEncontrado = $this->repository->findById($uuid);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(ColaMail::class, $oEncontrado);
        $this->assertEquals($uuid, $oEncontrado->getUuid_item()->value());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oColaMail = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($oColaMail);
    }

    public function test_eliminar_cola_mail()
    {
        $oColaMail = $this->factory->createSimple();
        $uuid = $oColaMail->getUuid_item()->value();
        $this->repository->Guardar($oColaMail);

        $oGuardado = $this->repository->findById($uuid);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($uuid);
        $this->assertNull($oEliminado);
    }
}
