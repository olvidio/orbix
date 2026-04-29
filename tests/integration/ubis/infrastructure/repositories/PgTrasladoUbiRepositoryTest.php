<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\TrasladoUbiRepositoryInterface;
use Tests\myTest;

class PgTrasladoUbiRepositoryTest extends myTest
{
    private TrasladoUbiRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TrasladoUbiRepositoryInterface::class);
    }

    public function test_repository_se_resuelve_desde_el_contenedor()
    {
        $this->assertInstanceOf(TrasladoUbiRepositoryInterface::class, $this->repository);
    }
}
