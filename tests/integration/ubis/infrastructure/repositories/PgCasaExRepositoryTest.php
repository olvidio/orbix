<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\entity\Casa;
use Tests\factories\ubis\CasaFactory;
use Tests\myTest;

class PgCasaExRepositoryTest extends myTest
{
    private CasaExRepositoryInterface $repository;
    private CasaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CasaExRepositoryInterface::class);
        $this->factory = new CasaFactory();
    }

    public function test_guardar_eliminar_casa()
    {
        $o = $this->factory->createSimple();
        $id = $o->getId_ubi();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Casa::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
