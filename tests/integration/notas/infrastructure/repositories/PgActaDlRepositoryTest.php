<?php

namespace Tests\integration\notas\infrastructure\persistence\postgresql;

use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\entity\Acta;
use Tests\factories\notas\ActaFactory;
use Tests\myTest;

class PgActaDlRepositoryTest extends myTest
{
    private ActaDlRepositoryInterface $repository;
    private ActaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
        $this->factory = new ActaFactory();
    }

    public function test_guardar_eliminar_acta()
    {
        $o = $this->factory->createSimple();
        $id = $o->getActa();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Acta::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById('zzz inexistente dl 99/99'));
    }
}
