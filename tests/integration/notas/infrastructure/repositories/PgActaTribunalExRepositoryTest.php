<?php

namespace Tests\integration\notas\infrastructure\persistence\postgresql;

use src\notas\domain\contracts\ActaTribunalExRepositoryInterface;
use src\notas\domain\entity\ActaTribunal;
use Tests\factories\notas\ActaTribunalFactory;
use Tests\myTest;

class PgActaTribunalExRepositoryTest extends myTest
{
    private ActaTribunalExRepositoryInterface $repository;
    private ActaTribunalFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActaTribunalExRepositoryInterface::class);
        $this->factory = new ActaTribunalFactory();
    }

    public function test_guardar_eliminar_acta_tribunal()
    {
        $o = $this->factory->createSimple();
        $idItem = (int) $this->repository->getNewId();
        $o->setId_item($idItem);
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($idItem);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(ActaTribunal::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($idItem));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
