<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\PosiblesOpcionalesData;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;

final class PosiblesOpcionalesDataTest extends TestCase
{
    public function test_excluye_opcionales_ya_superadas(): void
    {
        $a1 = $this->createMock(\src\asignaturas\domain\entity\Asignatura::class);
        $a1->method('getId_asignatura')->willReturn(3100);
        $a1->method('getNombre_corto')->willReturn('Opt A');

        $a2 = $this->createMock(\src\asignaturas\domain\entity\Asignatura::class);
        $a2->method('getId_asignatura')->willReturn(3200);
        $a2->method('getNombre_corto')->willReturn('Opt B');

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->expects($this->once())
            ->method('getAsignaturas')
            ->willReturn([$a1, $a2]);

        $pnSuperada = $this->createMock(PersonaNota::class);
        $pnSuperada->method('getId_asignatura')->willReturn(3100);

        $pnRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $pnRepo->expects($this->once())
            ->method('getPersonaNotas')
            ->willReturn([$pnSuperada]);

        $useCase = new PosiblesOpcionalesData($asigRepo, $pnRepo);
        $out = $useCase->execute(['id_nom' => 77]);
        $this->assertSame([3200 => 'Opt B'], $out);
    }

    public function test_todas_disponibles_si_no_hay_superadas(): void
    {
        $a1 = $this->createMock(\src\asignaturas\domain\entity\Asignatura::class);
        $a1->method('getId_asignatura')->willReturn(4001);
        $a1->method('getNombre_corto')->willReturn('X');

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('getAsignaturas')->willReturn([$a1]);

        $pnRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $pnRepo->method('getPersonaNotas')->willReturn([]);

        $useCase = new PosiblesOpcionalesData($asigRepo, $pnRepo);
        $this->assertSame([4001 => 'X'], $useCase->execute(['id_nom' => 1]));
    }
}
