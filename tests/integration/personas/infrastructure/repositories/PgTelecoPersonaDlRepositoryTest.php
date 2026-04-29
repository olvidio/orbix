<?php

namespace Tests\integration\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\entity\TelecoPersona;
use Tests\factories\personas\TelecoPersonaFactory;
use Tests\myTest;

class PgTelecoPersonaDlRepositoryTest extends myTest
{
    private TelecoPersonaDlRepositoryInterface $repository;
    private TelecoPersonaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TelecoPersonaDlRepositoryInterface::class);
        $this->factory = new TelecoPersonaFactory();
    }

    public function test_guardar_eliminar_teleco_persona()
    {
        $o = $this->factory->createSimple();
        $nid = $this->repository->getNewId();
        $o->setId_item((int) $nid);
        $id = $o->getId_item();

        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(TelecoPersona::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
