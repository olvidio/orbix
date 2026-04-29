<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\entity\Direccion;
use Tests\factories\ubis\DireccionFactory;
use Tests\myTest;

class PgDireccionCentroExRepositoryTest extends myTest
{
    private DireccionCentroExRepositoryInterface $repository;
    private DireccionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DireccionCentroExRepositoryInterface::class);
        $this->factory = new DireccionFactory();
    }

    public function test_guardar_eliminar_direccion()
    {
        $o = $this->factory->createSimple();
        $id = $o->getId_direccion();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Direccion::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
