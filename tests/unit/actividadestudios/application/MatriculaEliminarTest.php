<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\MatriculaEliminar;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\dossiers\domain\contracts\DossierRepositoryInterface;

final class MatriculaEliminarTest extends TestCase
{
    public function test_pau_vacio_devuelve_cadena_vacia(): void
    {
        $useCase = new MatriculaEliminar(
            $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            $this->createMock(MatriculaDlRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class),
        );

        $this->assertSame('', $useCase->execute([]));
    }

    public function test_p_a_sin_matricula(): void
    {
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('findById')->with(1, 2, 3)->willReturn(null);

        $useCase = new MatriculaEliminar(
            $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            $matRepo,
            $this->createMock(DossierRepositoryInterface::class),
        );

        $msg = $useCase->execute([
            'pau' => 'a',
            'id_activ' => 1,
            'id_asignatura' => 2,
            'id_nom' => 3,
        ]);
        $this->assertStringContainsString('no encuentro la matricula', $msg);
    }

    public function test_p_a_exito_y_cierra_dossier_cuando_existe(): void
    {
        $oMat = $this->createMock(Matricula::class);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('findById')->with(1, 2, 3)->willReturn($oMat);
        $matRepo->method('Eliminar')->with($oMat)->willReturn(true);

        $dossierRepo = $this->createMock(DossierRepositoryInterface::class);
        $dossierRepo->expects($this->once())->method('findByPk');
        $dossierRepo->expects($this->never())->method('Guardar');

        $useCase = new MatriculaEliminar(
            $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            $matRepo,
            $dossierRepo,
        );

        $msg = $useCase->execute([
            'pau' => 'a',
            'id_activ' => 1,
            'id_asignatura' => 2,
            'id_nom' => 3,
        ]);
        $this->assertSame('', $msg);
    }

    public function test_p_a_error_al_eliminar(): void
    {
        $oMat = $this->createMock(Matricula::class);
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('findById')->willReturn($oMat);
        $matRepo->method('Eliminar')->willReturn(false);

        $useCase = new MatriculaEliminar(
            $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            $matRepo,
            $this->createMock(DossierRepositoryInterface::class),
        );

        $msg = $useCase->execute([
            'pau' => 'a',
            'id_activ' => 1,
            'id_nom' => 3,
            'id_asignatura' => 2,
        ]);
        $this->assertStringContainsString('no se ha borrado', $msg);
    }

    public function test_p_p_borra_asignatura_impartida_si_queda_huerfana(): void
    {
        $oMat = $this->createMock(Matricula::class);
        $oAa = $this->createMock(ActividadAsignatura::class);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('findById')->with(10, 20, 30)->willReturn($oMat);
        $matRepo->method('Eliminar')->with($oMat)->willReturn(true);
        $matRepo->method('getMatriculas')->with([
            'id_activ' => 10,
            'id_asignatura' => 20,
        ])->willReturn([]);

        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->with([
            'id_activ' => 10,
            'id_asignatura' => 20,
        ])->willReturn([$oAa]);
        $aaRepo->expects($this->once())->method('Eliminar')->with($oAa)->willReturn(true);

        $useCase = new MatriculaEliminar($aaRepo, $matRepo, $this->createMock(DossierRepositoryInterface::class));

        $msg = $useCase->execute([
            'pau' => 'p',
            'sel' => ['10#20#30'],
        ]);
        $this->assertSame('', $msg);
    }
}
