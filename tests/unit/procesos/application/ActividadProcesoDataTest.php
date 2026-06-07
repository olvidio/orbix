<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\procesos\application\ActividadProcesoData;

final class ActividadProcesoDataTest extends TestCase
{
    public function test_actividad_no_encontrada(): void
    {
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn(null);

        $useCase = new ActividadProcesoData($repo);
        $out = $useCase->execute(9);
        $this->assertSame(9, $out['id_activ']);
        $this->assertSame('', $out['nom_activ']);
    }

    public function test_actividad_encontrada(): void
    {
        $act = $this->createMock(ActividadAll::class);
        $act->method('getNom_activ')->willReturn('Curso X');

        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn($act);

        $useCase = new ActividadProcesoData($repo);
        $out = $useCase->execute(3);
        $this->assertSame('Curso X', $out['nom_activ']);
    }
}
