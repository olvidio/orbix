<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\NivelCatalogoAsignaturaEnPlan;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;

final class NivelCatalogoAsignaturaEnPlanTest extends TestCase
{
    public function test_latin_iv_plan_2026_usa_hueco_2212(): void
    {
        $latin4 = $this->createMock(Asignatura::class);
        $latin4->method('getId_nivel')->willReturn(2212);
        $latin4->method('isActive')->willReturn(true);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')
            ->with(2312, PlanEstudios::PLAN_2026)
            ->willReturn($latin4);

        $resolver = new NivelCatalogoAsignaturaEnPlan($asigRepo, $this->planEstudiosDePersona());

        $this->assertSame(2212, $resolver->resolve(103615, 2312));
    }

    public function test_latin_iv_plan_1997_usa_hueco_2312(): void
    {
        $latin4 = $this->createMock(Asignatura::class);
        $latin4->method('getId_nivel')->willReturn(2312);
        $latin4->method('isActive')->willReturn(true);

        $finCuad = $this->createMock(\src\notas\domain\entity\PersonaNota::class);
        $finCuad->method('getF_acta')->willReturn(new \src\shared\domain\value_objects\DateTimeLocal('2020-01-01'));

        $pnRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $pnRepo->method('getPersonaNotas')->willReturn([$finCuad]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')
            ->with(2312, PlanEstudios::PLAN_1997)
            ->willReturn($latin4);

        $resolver = new NivelCatalogoAsignaturaEnPlan($asigRepo, new PlanEstudiosDePersona($pnRepo));

        $this->assertSame(2312, $resolver->resolve(1, 2312));
    }

    private function planEstudiosDePersona(): PlanEstudiosDePersona
    {
        $pnRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $pnRepo->method('getPersonaNotas')->willReturn([]);

        return new PlanEstudiosDePersona($pnRepo);
    }
}
