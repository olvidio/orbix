<?php

declare(strict_types=1);

namespace Tests\integration\zonassacd\infrastructure\repositories;

use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;
use Tests\factories\zonassacd\ZonaCtrFactory;
use Tests\myTest;

class PgZonaCtrRepositoryTest extends myTest
{
    private ZonaCtrRepositoryInterface $repository;
    private ZonaCtrFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ZonaCtrRepositoryInterface::class);
        $this->factory = new ZonaCtrFactory();
    }

    public function test_asignar_consultar_y_quitar(): void
    {
        $fila = $this->factory->createSimple();
        $idUbi = $fila->getId_ubi();
        $idZona = $fila->getId_zona();

        $this->assertTrue($this->repository->asignar($idUbi, $idZona));
        $this->assertSame($idZona, $this->repository->zonaDeCentro($idUbi));
        $this->assertContains($idUbi, $this->repository->idUbisDeZona($idZona));
        $this->assertSame([$idUbi => $idZona], $this->repository->mapaZonaPorCentro([$idUbi]));

        $this->assertTrue($this->repository->asignar($idUbi, null));
        $this->assertNull($this->repository->zonaDeCentro($idUbi));
    }
}
