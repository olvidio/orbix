<?php

namespace Tests\integration\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\entity\PersonaNax;
use Tests\factories\personas\PersonaNaxFactory;
use Tests\myTest;

class PgPersonaNaxRepositoryTest extends myTest
{
    private PersonaNaxRepositoryInterface $repository;
    private PersonaNaxFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
        $this->factory = new PersonaNaxFactory();
    }

    public function test_guardar_eliminar_persona_nax()
    {
        $o = $this->factory->createSimple();
        $id = $o->getId_nom();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(PersonaNax::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
