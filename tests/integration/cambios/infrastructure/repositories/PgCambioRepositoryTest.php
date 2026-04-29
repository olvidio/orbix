<?php

namespace Tests\integration\cambios\infrastructure\persistence\postgresql;

use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use Tests\factories\cambios\CambioFactory;
use Tests\myTest;

class PgCambioRepositoryTest extends myTest
{
    private CambioRepositoryInterface $repository;
    private CambioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
        $this->factory = new CambioFactory();
    }

    public function test_guardar_eliminar_cambio()
    {
        $o = $this->factory->create();
        $id = (int) $this->repository->getNewId();
        $o->setId_item_cambio($id);
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertSame($id, $oGuardado->getId_item_cambio());

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999981));
    }
}
