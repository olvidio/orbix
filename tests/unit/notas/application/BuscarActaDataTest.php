<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\BuscarActaData;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\NivelCatalogoAsignaturaEnPlan;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\shared\domain\value_objects\DateTimeLocal;

final class BuscarActaDataTest extends TestCase
{
    public function test_latin_iii_plan_2026_devuelve_hueco_2112_no_2212(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->method('getId_asignatura')->willReturn(2211);
        $acta->method('getId_activ')->willReturn(null);
        $acta->method('getActa')->willReturn('dlmE 39/26');
        $acta->method('getF_acta')->willReturn(new DateTimeLocal('2026-06-01'));

        $actaDl = $this->createMock(ActaDlRepositoryInterface::class);
        $actaDl->method('getActas')
            ->with(['acta' => 'dlmE 39/26'])
            ->willReturn([$acta]);

        $latin3 = $this->createMock(Asignatura::class);
        $latin3->method('getId_nivel')->willReturn(2112);
        $latin3->method('isActive')->willReturn(true);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->expects($this->once())
            ->method('findById')
            ->with(2211, PlanEstudios::PLAN_2026)
            ->willReturn($latin3);

        $out = $this->useCase($actaDl, $asigRepo)->execute([
            'acta' => '39/26',
            'acta_sigla' => 'dlmE',
            'id_pau' => 103615,
        ]);

        $this->assertSame('2211', $out['id_asignatura']);
        $this->assertSame('2112', $out['id_nivel']);
        $this->assertSame('dlmE 39/26', $out['acta']);
    }

    public function test_sin_id_pau_usa_catalogo_sin_plan(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->method('getId_asignatura')->willReturn(2211);
        $acta->method('getId_activ')->willReturn(null);
        $acta->method('getActa')->willReturn('dlmE 39/26');
        $acta->method('getF_acta')->willReturn(null);

        $actaDl = $this->createMock(ActaDlRepositoryInterface::class);
        $actaDl->method('getActas')->willReturn([$acta]);

        $filaArbitraria = $this->createMock(Asignatura::class);
        $filaArbitraria->method('getId_nivel')->willReturn(2212);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->expects($this->once())
            ->method('findById')
            ->with(2211)
            ->willReturn($filaArbitraria);

        $out = $this->useCase($actaDl, $asigRepo)->execute([
            'acta' => '39/26',
            'acta_sigla' => 'dlmE',
        ]);

        $this->assertSame('2212', $out['id_nivel']);
    }

    public function test_acta_inexistente_devuelve_no(): void
    {
        $actaDl = $this->createMock(ActaDlRepositoryInterface::class);
        $actaDl->method('getActas')->willReturn([]);
        $actaEx = $this->createMock(ActaExRepositoryInterface::class);
        $actaEx->method('getActas')->willReturn([]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->expects($this->never())->method('findById');

        $out = $this->useCase($actaDl, $asigRepo, $actaEx)->execute([
            'acta' => '39/26',
            'acta_sigla' => 'dlmE',
            'id_pau' => 1,
        ]);

        $this->assertSame('no', $out['id_asignatura']);
    }

    private function useCase(
        ActaDlRepositoryInterface $actaDl,
        AsignaturaRepositoryInterface $asigRepo,
        ?ActaExRepositoryInterface $actaEx = null,
    ): BuscarActaData {
        $actaEx ??= $this->createMock(ActaExRepositoryInterface::class);
        $plan = $this->planEstudiosDePersona();

        return new BuscarActaData(
            $actaDl,
            $actaEx,
            $this->createMock(ActividadAllRepositoryInterface::class),
            $asigRepo,
            new NivelCatalogoAsignaturaEnPlan($asigRepo, $plan),
        );
    }

    private function planEstudiosDePersona(): PlanEstudiosDePersona
    {
        $pnRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $pnRepo->method('getPersonaNotas')->willReturn([]);

        return new PlanEstudiosDePersona($pnRepo);
    }
}
