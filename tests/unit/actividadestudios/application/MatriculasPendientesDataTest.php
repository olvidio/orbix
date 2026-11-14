<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\ActividadNomText;
use src\actividadestudios\application\MatriculasPendientesData;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use Tests\support\DescribeCriteria;
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

        $useCase = new MatriculasPendientesData($matRepo, $asigRepo, $actRepo, $finder, $this->createMock(PersonaNotaDlRepositoryInterface::class));

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
        $oMat->method('getIdAsignaturaVo')->willReturn(new AsignaturaId(3000));
        $oMat->method('isPreceptor')->willReturn(false);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([$oMat]);

        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNomActivVo')->willReturn(new ActividadNomText('CA X'));

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(200)->willReturn($oActiv);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getPrefApellidosNombre')->willReturn('García, Ana');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaParaListado')->willReturn($oPersona);

        $oAsig = $this->createMock(Asignatura::class);
        $oAsig->method('getNombre_corto')->willReturn('MAT1');

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->with(3000)->willReturn($oAsig);

        $useCase = new MatriculasPendientesData($matRepo, $asigRepo, $actRepo, $finder, $this->createMock(PersonaNotaDlRepositoryInterface::class));

        $out = $useCase->execute();
        $this->assertSame('', $out['msg_err']);
        $this->assertCount(1, $out['a_valores']);
        $row = array_values($out['a_valores'])[0];
        $this->assertSame('200#3000#100', $row['sel']);
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
        $oMat1->method('getIdAsignaturaVo')->willReturn(new AsignaturaId(3000));
        $oMat1->method('isPreceptor')->willReturn(false);

        $oMat2 = $this->createMock(Matricula::class);
        $oMat2->method('getId_nom')->willReturn(100);
        $oMat2->method('getId_activ')->willReturn(200);
        $oMat2->method('getIdAsignaturaVo')->willReturn(new AsignaturaId(3001));
        $oMat2->method('isPreceptor')->willReturn(true);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([$oMat1, $oMat2]);

        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNomActivVo')->willReturn(new ActividadNomText('CA X'));

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
            [3000, $oAsig1],
            [3001, $oAsig2],
        ]);

        $useCase = new MatriculasPendientesData($matRepo, $asigRepo, $actRepo, $finder, $this->createMock(PersonaNotaDlRepositoryInterface::class));

        $out = $useCase->execute();
        $this->assertCount(2, $out['a_valores']);
        $rows = array_values($out['a_valores']);
        $sels = array_column($rows, 'sel');
        $this->assertContains('200#3000#100', $sels);
        $this->assertContains('200#3001#100', $sels);
    }

    public function test_persona_no_encontrada_solo_un_aviso(): void
    {
        $oMat1 = $this->createMock(Matricula::class);
        $oMat1->method('getId_nom')->willReturn(100);
        $oMat1->method('getId_activ')->willReturn(200);
        $oMat1->method('getIdAsignaturaVo')->willReturn(new AsignaturaId(3000));
        $oMat1->method('isPreceptor')->willReturn(false);

        $oMat2 = $this->createMock(Matricula::class);
        $oMat2->method('getId_nom')->willReturn(100);
        $oMat2->method('getId_activ')->willReturn(200);
        $oMat2->method('getIdAsignaturaVo')->willReturn(new AsignaturaId(3001));
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
            $this->createMock(PersonaNotaDlRepositoryInterface::class),
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
        $oMat1->method('getIdAsignaturaVo')->willReturn(new AsignaturaId(3000));
        $oMat1->method('isPreceptor')->willReturn(false);

        $oMat2 = $this->createMock(Matricula::class);
        $oMat2->method('getId_nom')->willReturn(100);
        $oMat2->method('getId_activ')->willReturn(200);
        $oMat2->method('getIdAsignaturaVo')->willReturn(new AsignaturaId(3001));
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
            $this->createMock(PersonaNotaDlRepositoryInterface::class),
        );

        $out = $useCase->execute();
        $this->assertSame([], $out['a_valores']);
        $this->assertSame(1, substr_count($out['msg_err'], 'actividad con id: 200'));
        $this->assertSame(1, substr_count($out['msg_err'], 'alumno id_nom: 100'));
    }

    /**
     * Persona que ya no está: la matrícula pendiente se borra, aunque tenga acta. Solo se conserva (con
     * aviso) si la propia matrícula lleva nota y esa nota no está en `e_notas_dl` (duda 18 del plan).
     */
    public function test_persona_no_encontrada_borra_la_matricula_salvo_si_lleva_una_nota_sin_guardar(): void
    {
        $matricula = function (int $idAsignatura, ?string $acta, ?string $notaNum): Matricula {
            $m = $this->createMock(Matricula::class);
            $m->method('getId_nom')->willReturn(100);
            $m->method('getId_activ')->willReturn(200);
            $m->method('getIdAsignaturaVo')->willReturn(new AsignaturaId($idAsignatura));
            $m->method('isPreceptor')->willReturn(false);
            $m->method('getActa')->willReturn($acta);
            $m->method('getNota_num')->willReturn($notaNum);
            return $m;
        };

        $borradas = [];
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([
            $matricula(3000, null, null),          // sin acta ni nota
            $matricula(3001, 'dlb 1/25', null),    // con acta pero sin nota: se borra igualmente
            $matricula(3002, 'dlb 2/25', '8'),     // con nota, y la nota está en e_notas_dl
            $matricula(3003, 'dlb 3/25', '7'),     // con nota que no está guardada: se conserva
        ]);
        $matRepo->method('Eliminar')->willReturnCallback(function (Matricula $m) use (&$borradas): bool {
            $borradas[] = $m->getIdAsignaturaVo()->value();
            return true;
        });
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaParaListado')->willReturn(null);
        $notas = $this->createMock(PersonaNotaDlRepositoryInterface::class);
        $notas->method('matching')->willReturnCallback(static fn ($c): array
            => DescribeCriteria::filtros($c) === ['id_nom eq 100', 'id_asignatura eq 3002'] ? [new PersonaNota()] : []);

        $out = (new MatriculasPendientesData(
            $matRepo,
            $this->createMock(AsignaturaRepositoryInterface::class),
            $this->createMock(ActividadAllRepositoryInterface::class),
            $finder,
            $notas,
        ))->execute();

        $this->assertSame([3000, 3001, 3002], $borradas);
        $this->assertSame([], $out['a_valores']);
        $this->assertStringContainsString('asignatura 3003, acta dlb 3/25', $out['msg_err']);
        $this->assertSame(1, substr_count($out['msg_err'], 'no se borra'));
    }
}
