<?php

namespace Tests\integration\notas\infrastructure\persistence\postgresql;

use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\entity\ActaTribunal;
use Tests\factories\notas\ActaTribunalFactory;
use Tests\myTest;

class PgActaTribunalDlRepositoryTest extends myTest
{
    private ActaTribunalDlRepositoryInterface $repository;
    private ActaTribunalFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class);
        $this->factory = new ActaTribunalFactory();
    }

    public function test_guardar_eliminar_acta_tribunal()
    {
        $o = $this->factory->createSimple();
        $idItem = (int) $this->repository->getNewId();
        $o->setId_item($idItem);
        $id = $o->getId_item();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(ActaTribunal::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
