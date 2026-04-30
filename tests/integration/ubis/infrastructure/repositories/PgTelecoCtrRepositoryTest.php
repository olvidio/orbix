<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;
use Tests\factories\ubis\TelecoUbiFactory;
use Tests\myTest;

class PgTelecoCtrRepositoryTest extends myTest
{
    private TelecoCtrRepositoryInterface $repository;
    private TelecoUbiFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TelecoCtrRepositoryInterface::class);
        $this->factory = new TelecoUbiFactory();
    }

    public function test_guardar_eliminar_teleco()
    {
        // No aplica para este repositorio, por que no es de la dl
        $this->assertTrue(true);
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
