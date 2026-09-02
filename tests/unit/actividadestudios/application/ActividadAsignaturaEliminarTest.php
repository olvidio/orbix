<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\ActividadAsignaturaEliminar;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;

final class ActividadAsignaturaEliminarTest extends TestCase
{
    public function test_pau_distinto_de_a_devuelve_mensaje(): void
    {
        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->expects($this->never())->method('findById');

        $out = $this->useCase($aaRepo)->execute(['pau' => 'p', 'id_activ' => 1, 'id_asignatura' => 2]);
        $this->assertFalse($out['requiere_confirmacion']);
        $this->assertNotSame('', $out['error']);
    }

    public function test_faltan_claves_devuelve_mensaje(): void
    {
        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->expects($this->never())->method('findById');

        $out = $this->useCase($aaRepo)->execute(['pau' => 'a', 'id_activ' => 0, 'id_asignatura' => 2]);
        $this->assertStringContainsString('faltan', $out['error']);
    }

    public function test_no_existe_asignatura(): void
    {
        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->method('findById')->with(10, 20)->willReturn(null);

        $out = $this->useCase($aaRepo)->execute([
            'pau' => 'a',
            'id_activ' => 10,
            'id_asignatura' => 20,
        ]);
        $this->assertStringContainsString('no encuentro', $out['error']);
    }

    public function test_eliminar_falla_devuelve_error(): void
    {
        $oAa = $this->createMock(ActividadAsignatura::class);
        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->method('findById')->willReturn($oAa);
        $aaRepo->method('Eliminar')->with($oAa)->willReturn(false);

        $out = $this->useCase($aaRepo)->execute([
            'pau' => 'a',
            'id_activ' => 10,
            'id_asignatura' => 20,
        ]);
        $this->assertStringContainsString('no se ha borrado', $out['error']);
    }

    public function test_exito_parseando_sel_sin_matriculas(): void
    {
        $oAa = $this->createMock(ActividadAsignatura::class);
        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->method('findById')->with(7, 8)->willReturn($oAa);
        $aaRepo->expects($this->once())->method('Eliminar')->with($oAa)->willReturn(true);

        $out = $this->useCase($aaRepo)->execute([
            'pau' => 'a',
            'sel' => ['7#8#true#1001'],
        ]);
        $this->assertSame('', $out['error']);
        $this->assertFalse($out['requiere_confirmacion']);
    }

    public function test_con_matriculas_pide_confirmacion_y_no_borra(): void
    {
        $oAa = $this->createMock(ActividadAsignatura::class);
        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->method('findById')->willReturn($oAa);
        $aaRepo->expects($this->never())->method('Eliminar');

        $m1 = $this->matricula(11);
        $m2 = $this->matricula(12);
        $m3 = $this->matricula(13);
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturn([$m1, $m2, $m3]);
        $matRepo->expects($this->never())->method('Eliminar');

        $pB = $this->persona('dlbv');
        $pX = $this->persona('dlxx');
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(
            static function (int $idNom) use ($pB, $pX) {
                return match ($idNom) {
                    11, 12 => $pB,
                    13 => $pX,
                    default => null,
                };
            }
        );

        $out = $this->useCase($aaRepo, $matRepo, $finder)->execute([
            'pau' => 'a',
            'id_activ' => 10,
            'id_asignatura' => 20,
        ]);

        $this->assertTrue($out['requiere_confirmacion']);
        $this->assertSame('', $out['error']);
        $this->assertStringContainsString('3', $out['mensaje']);
        $this->assertStringContainsString('dlbv: 2', $out['mensaje']);
        $this->assertStringContainsString('dlxx: 1', $out['mensaje']);
        $this->assertStringContainsString('matrícula', $out['mensaje']);
    }

    public function test_confirmar_borra_matriculas_y_asignatura(): void
    {
        $oAa = $this->createMock(ActividadAsignatura::class);
        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->method('findById')->willReturn($oAa);
        $aaRepo->expects($this->once())->method('Eliminar')->with($oAa)->willReturn(true);

        $m1 = $this->matricula(11);
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturn([$m1]);
        $matRepo->expects($this->once())->method('Eliminar')->with($m1)->willReturn(true);

        $out = $this->useCase($aaRepo, $matRepo)->execute([
            'pau' => 'a',
            'id_activ' => 10,
            'id_asignatura' => 20,
            'confirmar_con_matriculas' => '1',
        ]);

        $this->assertFalse($out['requiere_confirmacion']);
        $this->assertSame('', $out['error']);
    }

    public function test_mensaje_singular(): void
    {
        $msg = ActividadAsignaturaEliminar::mensajeConMatriculas(1, ['dlbv' => 1]);
        $this->assertStringContainsString('1 alumno', $msg);
        $this->assertStringContainsString('dlbv: 1', $msg);
    }

    private function matricula(int $idNom): Matricula
    {
        $o = $this->createMock(Matricula::class);
        $o->method('getId_nom')->willReturn($idNom);

        return $o;
    }

    private function persona(string $dl): PersonaDl
    {
        $o = $this->createMock(PersonaDl::class);
        $o->method('getDl')->willReturn($dl);

        return $o;
    }

    private function useCase(
        ActividadAsignaturaDlRepositoryInterface $aaRepo,
        ?MatriculaDlRepositoryInterface $matRepo = null,
        ?PersonaFinderService $finder = null,
    ): ActividadAsignaturaEliminar {
        $mat = $matRepo ?? $this->createMock(MatriculaDlRepositoryInterface::class);
        if ($matRepo === null) {
            $mat->method('getMatriculas')->willReturn([]);
        }

        return new ActividadAsignaturaEliminar(
            $aaRepo,
            $mat,
            $finder ?? $this->createMock(PersonaFinderService::class),
        );
    }
}
