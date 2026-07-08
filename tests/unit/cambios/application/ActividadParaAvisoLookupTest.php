<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\cambios\application\ActividadParaAvisoLookup;

final class ActividadParaAvisoLookupTest extends TestCase
{
    public function test_find_devuelve_actividad_all_si_existe(): void
    {
        $actividad = new ActividadAll();
        $actividad->setId_activ(10);

        $allRepository = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepository->expects($this->once())->method('findById')->with(10)->willReturn($actividad);

        $exRepository = $this->createMock(ActividadExRepositoryInterface::class);
        $exRepository->expects($this->never())->method('findById');

        $lookup = new ActividadParaAvisoLookup($allRepository, $exRepository);

        $this->assertSame($actividad, $lookup->find(10));
    }

    public function test_find_usa_ex_si_no_esta_en_all(): void
    {
        $actividad = new ActividadAll();
        $actividad->setId_activ(99);

        $allRepository = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepository->method('findById')->willReturn(null);

        $exRepository = $this->createMock(ActividadExRepositoryInterface::class);
        $exRepository->expects($this->once())->method('findById')->with(99)->willReturn($actividad);

        $lookup = new ActividadParaAvisoLookup($allRepository, $exRepository);

        $this->assertSame($actividad, $lookup->find(99));
    }

    public function test_find_devuelve_null_para_id_invalido(): void
    {
        $allRepository = $this->createMock(ActividadAllRepositoryInterface::class);
        $exRepository = $this->createMock(ActividadExRepositoryInterface::class);
        $allRepository->expects($this->never())->method('findById');
        $exRepository->expects($this->never())->method('findById');

        $lookup = new ActividadParaAvisoLookup($allRepository, $exRepository);

        $this->assertNull($lookup->find(0));
    }
}
