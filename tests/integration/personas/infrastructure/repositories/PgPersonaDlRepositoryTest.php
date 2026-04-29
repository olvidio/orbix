<?php

namespace Tests\integration\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use Tests\myTest;

class PgPersonaDlRepositoryTest extends myTest
{
    private PersonaDlRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
