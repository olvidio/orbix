<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\support\NivelesOcupadosEnPlan;
use src\notas\domain\entity\PersonaNota;

final class NivelesOcupadosEnPlanTest extends TestCase
{
    public function test_latin_iii_antiguo_ocupa_hueco_2026_de_latin_iii_no_el_de_latin_iv(): void
    {
        $latin3Plan2026 = $this->asignatura(2211, 2112);
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->method('findById')->willReturnCallback(
            static function (int $id, int|array|null $plan) use ($latin3Plan2026): ?Asignatura {
                self::assertSame(PlanEstudios::PLAN_2026, $plan);

                return $id === 2211 ? $latin3Plan2026 : null;
            }
        );

        $notaLatin3Antigua = $this->nota(2211, 2212);

        $ocupados = NivelesOcupadosEnPlan::ocupados(
            [$notaLatin3Antigua],
            PlanEstudios::PLAN_2026,
            $repo,
        );

        $this->assertSame([2112 => true], $ocupados);
        $this->assertArrayNotHasKey(2212, $ocupados);
    }

    public function test_latin_iv_con_nivel_1997_ocupa_hueco_2026_de_latin_iv(): void
    {
        $latin4Plan2026 = $this->asignatura(2312, 2212);
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->method('findById')->willReturn($latin4Plan2026);

        $ocupados = NivelesOcupadosEnPlan::ocupados(
            [$this->nota(2312, 2312)],
            PlanEstudios::PLAN_2026,
            $repo,
        );

        $this->assertSame([2212 => true], $ocupados);
    }

    public function test_opcional_ocupa_el_nivel_de_la_nota(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $ocupados = NivelesOcupadosEnPlan::ocupados(
            [$this->nota(3411, 2430)],
            PlanEstudios::PLAN_2026,
            $repo,
        );

        $this->assertSame([2430 => true], $ocupados);
    }

    public function test_asignatura_solo_del_otro_plan_no_ocupa_hueco(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $ocupados = NivelesOcupadosEnPlan::ocupados(
            [$this->nota(2112, 2112)],
            PlanEstudios::PLAN_2026,
            $repo,
        );

        $this->assertSame([], $ocupados);
    }

    private function asignatura(int $id, int $nivel): Asignatura
    {
        $asig = $this->createMock(Asignatura::class);
        $asig->method('getId_asignatura')->willReturn($id);
        $asig->method('getId_nivel')->willReturn($nivel);
        $asig->method('isActive')->willReturn(true);

        return $asig;
    }

    private function nota(int $idAsignatura, int $idNivel): PersonaNota
    {
        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getId_asignatura')->willReturn($idAsignatura);
        $nota->method('getId_nivel')->willReturn($idNivel);

        return $nota;
    }
}
