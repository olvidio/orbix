<?php

declare(strict_types=1);

namespace Tests\integration\zonassacd\infrastructure\repositories;

use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use Tests\factories\zonassacd\ZonaCtrFactory;
use Tests\factories\zonassacd\ZonaFactory;
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

    public function test_eliminar_por_zona_quita_solo_los_centros_de_esa_zona(): void
    {
        $zonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $oZona = (new ZonaFactory())->createSimple();
        $idZona = $oZona->getId_zona();
        $this->assertTrue($zonaRepository->Guardar($oZona));
        $otro = $this->factory->createSimple();
        try {
            $a = $this->factory->createSimple(null, $idZona)->getId_ubi();
            $b = $a + 1;
            $this->assertTrue($this->repository->asignar($a, $idZona));
            $this->assertTrue($this->repository->asignar($b, $idZona));
            $this->assertTrue($this->repository->asignar($otro->getId_ubi(), $otro->getId_zona()));

            $this->assertSame(2, $this->repository->eliminarPorZona($idZona));

            $this->assertSame([], $this->repository->idUbisDeZona($idZona));
            $this->assertSame($otro->getId_zona(), $this->repository->zonaDeCentro($otro->getId_ubi()));
            $this->assertSame(0, $this->repository->eliminarPorZona($idZona));
        } finally {
            $this->repository->asignar($otro->getId_ubi(), null);
            $zonaRepository->Eliminar($oZona);
        }
    }
}
