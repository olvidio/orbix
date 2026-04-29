<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;
use Tests\factories\ubis\TelecoUbiFactory;
use Tests\myTest;

class PgTelecoUbiRepositoryTest extends myTest
{
    private TelecoUbiRepositoryInterface $repository;
    private TelecoUbiFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TelecoUbiRepositoryInterface::class);
        $this->factory = new TelecoUbiFactory();
    }

    public function test_guardar_eliminar_teleco()
    {
        $o = $this->factory->createSimple();
        $nid = $this->repository->getNewId();
        $o->setId_item((int) $nid);
        $id = $o->getId_item();

        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(TelecoUbi::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
