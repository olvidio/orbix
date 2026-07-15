<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\AsignaturasSearchData;
use src\notas\application\PlanEstudiosDePersona;

final class AsignaturasSearchDataTest extends TestCase
{
    public function test_delega_en_repositorio_con_plan_por_defecto(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getJsonAsignaturas')
            ->with([
                'nombre_asignatura' => 'mat',
                'plan_estudios' => PlanEstudios::PLAN_2026,
            ])
            ->willReturn('[{"label":"x"}]');

        $plan = $this->createMock(PlanEstudiosDePersona::class);
        $plan->expects($this->never())->method('resolve');

        $useCase = new AsignaturasSearchData($repo, $plan);
        $this->assertSame('[{"label":"x"}]', $useCase->execute(['search' => 'mat']));
    }

    public function test_resuelve_plan_desde_id_nom(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getJsonAsignaturas')
            ->with([
                'nombre_asignatura' => 'mat',
                'plan_estudios' => PlanEstudios::PLAN_1997,
            ])
            ->willReturn('[]');

        $plan = $this->createMock(PlanEstudiosDePersona::class);
        $plan->expects($this->once())
            ->method('resolve')
            ->with(42)
            ->willReturn(PlanEstudios::PLAN_1997);

        $useCase = new AsignaturasSearchData($repo, $plan);
        $useCase->execute(['search' => 'mat', 'id_nom' => 42]);
    }
}
