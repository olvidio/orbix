<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\MatriculasPendientesData;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;

final class MatriculasPendientesDataTest extends TestCase
{
    public function test_lista_vacia(): void
    {
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->expects($this->never())->method('findById');

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->expects($this->never())->method('findById');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->never())->method('findPersonaParaListado');

        $useCase = new MatriculasPendientesData($matRepo, $asigRepo, $actRepo, $finder);

        $out = $useCase->execute();
        $this->assertSame('', $out['msg_err']);
        $this->assertSame('', $out['aviso']);
        $this->assertSame([], $out['a_valores']);
    }

    public function test_una_fila_completa(): void
    {
        $oMat = $this->createMock(Matricula::class);
        $oMat->method('getId_nom')->willReturn(100);
        $oMat->method('getId_activ')->willReturn(200);
        $oMat->method('getId_asignatura')->willReturn(300);
        $oMat->method('isPreceptor')->willReturn(false);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([$oMat]);

        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNom_activ')->willReturn('CA X');

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(200)->willReturn($oActiv);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getPrefApellidosNombre')->willReturn('García, Ana');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaParaListado')->willReturn($oPersona);

        $oAsig = $this->createMock(Asignatura::class);
        $oAsig->method('getNombre_corto')->willReturn('MAT1');

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->with(300)->willReturn($oAsig);

        $useCase = new MatriculasPendientesData($matRepo, $asigRepo, $actRepo, $finder);

        $out = $useCase->execute();
        $this->assertSame('', $out['msg_err']);
        $this->assertCount(1, $out['a_valores']);
        $row = array_values($out['a_valores'])[0];
        $this->assertSame('200#300#100', $row['sel']);
        $this->assertSame('CA X', $row[1]);
        $this->assertSame('MAT1', $row[2]);
        $this->assertSame('García, Ana', $row[3]);
        $this->assertSame('', $row[4]);
    }

    public function test_varias_matriculas_misma_persona_varias_filas_y_una_busqueda(): void
    {
        $oMat1 = $this->createMock(Matricula::class);
        $oMat1->method('getId_nom')->willReturn(100);
        $oMat1->method('getId_activ')->willReturn(200);
        $oMat1->method('getId_asignatura')->willReturn(300);
        $oMat1->method('isPreceptor')->willReturn(false);

        $oMat2 = $this->createMock(Matricula::class);
        $oMat2->method('getId_nom')->willReturn(100);
        $oMat2->method('getId_activ')->willReturn(200);
        $oMat2->method('getId_asignatura')->willReturn(301);
        $oMat2->method('isPreceptor')->willReturn(true);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([$oMat1, $oMat2]);

        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNom_activ')->willReturn('CA X');

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(200)->willReturn($oActiv);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getPrefApellidosNombre')->willReturn('García, Ana');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())->method('findPersonaParaListado')->willReturn($oPersona);

        $oAsig1 = $this->createMock(Asignatura::class);
        $oAsig1->method('getNombre_corto')->willReturn('MAT1');
        $oAsig2 = $this->createMock(Asignatura::class);
        $oAsig2->method('getNombre_corto')->willReturn('MAT2');

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->willReturnMap([
            [300, $oAsig1],
            [301, $oAsig2],
        ]);

        $useCase = new MatriculasPendientesData($matRepo, $asigRepo, $actRepo, $finder);

        $out = $useCase->execute();
        $this->assertCount(2, $out['a_valores']);
        $rows = array_values($out['a_valores']);
        $sels = array_column($rows, 'sel');
        $this->assertContains('200#300#100', $sels);
        $this->assertContains('200#301#100', $sels);
    }

    public function test_persona_no_encontrada_solo_un_aviso(): void
    {
        $oMat1 = $this->createMock(Matricula::class);
        $oMat1->method('getId_nom')->willReturn(100);
        $oMat1->method('getId_activ')->willReturn(200);
        $oMat1->method('getId_asignatura')->willReturn(300);
        $oMat1->method('isPreceptor')->willReturn(false);

        $oMat2 = $this->createMock(Matricula::class);
        $oMat2->method('getId_nom')->willReturn(100);
        $oMat2->method('getId_activ')->willReturn(200);
        $oMat2->method('getId_asignatura')->willReturn(301);
        $oMat2->method('isPreceptor')->willReturn(false);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([$oMat1, $oMat2]);

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())->method('findPersonaParaListado')->willReturn(null);

        $useCase = new MatriculasPendientesData(
            $matRepo,
            $this->createMock(AsignaturaRepositoryInterface::class),
            $this->createMock(ActividadAllRepositoryInterface::class),
            $finder,
        );

        $out = $useCase->execute();
        $this->assertSame([], $out['a_valores']);
        $this->assertSame(1, substr_count($out['msg_err'], 'id_nom: 100'));
    }

    public function test_actividad_no_encontrada_solo_un_aviso_por_alumno(): void
    {
        $oMat1 = $this->createMock(Matricula::class);
        $oMat1->method('getId_nom')->willReturn(100);
        $oMat1->method('getId_activ')->willReturn(200);
        $oMat1->method('getId_asignatura')->willReturn(300);
        $oMat1->method('isPreceptor')->willReturn(false);

        $oMat2 = $this->createMock(Matricula::class);
        $oMat2->method('getId_nom')->willReturn(100);
        $oMat2->method('getId_activ')->willReturn(200);
        $oMat2->method('getId_asignatura')->willReturn(301);
        $oMat2->method('isPreceptor')->willReturn(false);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([$oMat1, $oMat2]);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getPrefApellidosNombre')->willReturn('García, Ana');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaParaListado')->willReturn($oPersona);

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(200)->willReturn(null);

        $useCase = new MatriculasPendientesData(
            $matRepo,
            $this->createMock(AsignaturaRepositoryInterface::class),
            $actRepo,
            $finder,
        );

        $out = $useCase->execute();
        $this->assertSame([], $out['a_valores']);
        $this->assertSame(1, substr_count($out['msg_err'], 'actividad con id: 200'));
        $this->assertSame(1, substr_count($out['msg_err'], 'alumno id_nom: 100'));
    }
}
