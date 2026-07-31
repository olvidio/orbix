<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\AsignaturasSearchData;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\shared\domain\value_objects\DateTimeLocal;

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

        // Sin id_nom no se resuelve el plan: el repo de notas no debe consultarse.
        $repoPlan = $this->createMock(PersonaNotaRepositoryInterface::class);
        $repoPlan->expects($this->never())->method('getPersonaNotas');

        $useCase = new AsignaturasSearchData($repo, new PlanEstudiosDePersona($repoPlan));
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

        // Marca de cuadrienio (9998) con f_acta anterior a 2026-03-30 → plan 1997.
        $notaFinCuadrienio = $this->createMock(PersonaNota::class);
        $notaFinCuadrienio->method('getF_acta')->willReturn(new DateTimeLocal('2019-06-30'));

        $repoPlan = $this->createMock(PersonaNotaRepositoryInterface::class);
        $repoPlan->expects($this->once())
            ->method('getPersonaNotas')
            ->with(['id_nom' => 42, 'id_asignatura' => 9998])
            ->willReturn([$notaFinCuadrienio]);

        $useCase = new AsignaturasSearchData($repo, new PlanEstudiosDePersona($repoPlan));
        $useCase->execute(['search' => 'mat', 'id_nom' => 42]);
    }
}
