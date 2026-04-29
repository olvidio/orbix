<?php

namespace Tests\integration\asistentes\infrastructure\persistence\postgresql;

use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use Tests\factories\asistentes\AsistenteFactory;
use Tests\myTest;

class PgAsistenteOutRepositoryTest extends myTest
{
    private AsistenteOutRepositoryInterface $repository;
    private AsistenteFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AsistenteOutRepositoryInterface::class);
        $this->factory = new AsistenteFactory();
    }

    public function test_guardar_eliminar_asistente()
    {
        $o = $this->factory->createSimple();
        $ida = $o->getId_activ();
        $idb = $o->getId_nom();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($ida, $idb);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Asistente::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($ida, $idb));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999991, 999999992));
    }
}
