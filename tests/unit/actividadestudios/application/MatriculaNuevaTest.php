<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\ActividadAsignaturaNueva;
use src\actividadestudios\application\FormMatriculasDeUnaPersonaData;
use src\actividadestudios\application\MatriculaNueva;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;

final class MatriculaNuevaTest extends TestCase
{
    public function test_faltan_claves_no_guarda(): void
    {
        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->expects($this->never())->method('Guardar');

        $out = $this->useCase(
            $this->createMock(AsignaturaRepositoryInterface::class),
            $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            $this->createMock(ActividadAsignaturaRepositoryInterface::class),
            $mat,
            $this->createMock(DossierRepositoryInterface::class),
        )->execute([
            'id_activ' => 0,
            'id_pau' => 7,
            'id_asignatura' => 1101,
        ]);

        $this->assertFalse($out['requiere_confirmacion']);
        $this->assertNotSame('', $out['error']);
    }

    public function test_anadir_si_ya_existe_en_el_ca_pide_confirmacion(): void
    {
        $existente = new ActividadAsignatura();
        $existente->setId_schema(2002);
        $existente->setId_activ(10);
        $existente->setIdAsignaturaVo(1101);

        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->expects($this->never())->method('Guardar');

        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->expects($this->once())->method('getActividadAsignaturas')->with([
            'id_activ' => 10,
            'id_asignatura' => 1101,
        ])->willReturn([$existente]);

        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->expects($this->never())->method('Guardar');

        $out = $this->useCase(
            $this->createMock(AsignaturaRepositoryInterface::class),
            $dl,
            $all,
            $mat,
            $this->createMock(DossierRepositoryInterface::class),
        )->execute([
            'id_activ' => 10,
            'id_pau' => 7,
            'id_asignatura' => 1101,
            'id_nivel' => 1101,
        ]);

        $this->assertTrue($out['requiere_confirmacion']);
        $this->assertSame('', $out['error']);
        $this->assertSame(ActividadAsignaturaNueva::mensajeDuplicado(), $out['mensaje']);
    }

    public function test_anadir_con_confirmacion_crea_nueva_oferta(): void
    {
        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->method('findById')->willReturn(null);
        $mat->expects($this->once())->method('Guardar')->willReturn(true);

        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->expects($this->never())->method('getActividadAsignaturas');

        $existenteDl = new ActividadAsignatura();
        $existenteDl->setId_activ(10);
        $existenteDl->setIdAsignaturaVo(1101);
        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->method('getActividadAsignaturas')->willReturn([$existenteDl]);
        $dl->expects($this->once())->method('Guardar')->willReturn(true);

        $out = $this->useCase(
            $this->createMock(AsignaturaRepositoryInterface::class),
            $dl,
            $all,
            $mat,
            $this->dossiersQueGuardanPersona(),
        )->execute([
            'id_activ' => 10,
            'id_pau' => 7,
            'id_asignatura' => 1101,
            'id_nivel' => 1101,
            'confirmar_duplicado' => '1',
        ]);

        $this->assertFalse($out['requiere_confirmacion']);
        $this->assertSame('', $out['error']);
    }

    public function test_matricular_en_ca_no_pide_confirmacion_ni_crea_oferta(): void
    {
        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->method('findById')->willReturn(null);
        $mat->expects($this->once())->method('Guardar')->willReturn(true);

        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->expects($this->never())->method('getActividadAsignaturas');

        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->expects($this->never())->method('Guardar');

        $out = $this->useCase(
            $this->createMock(AsignaturaRepositoryInterface::class),
            $dl,
            $all,
            $mat,
            $this->dossiersQueGuardanPersona(),
        )->execute([
            'id_activ' => 10,
            'id_pau' => 7,
            'id_asignatura' => 1101,
            'id_nivel' => 1101,
            'modo' => FormMatriculasDeUnaPersonaData::MODO_MATRICULAR_CA,
        ]);

        $this->assertFalse($out['requiere_confirmacion']);
        $this->assertSame('', $out['error']);
    }

    public function test_matricular_en_ca_acepta_opcion_con_esquema(): void
    {
        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->method('findById')->willReturn(null);
        $mat->expects($this->once())->method('Guardar')->willReturnCallback(
            function ($oMatricula) {
                $this->assertSame(1101, $oMatricula->getId_asignatura());

                return true;
            }
        );

        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->expects($this->never())->method('Guardar');

        $out = $this->useCase(
            $this->createMock(AsignaturaRepositoryInterface::class),
            $dl,
            $this->createMock(ActividadAsignaturaRepositoryInterface::class),
            $mat,
            $this->dossiersQueGuardanPersona(),
        )->execute([
            'id_activ' => 10,
            'id_pau' => 7,
            'id_asignatura' => '1101#2002',
            'id_nivel' => 1101,
            'modo' => FormMatriculasDeUnaPersonaData::MODO_MATRICULAR_CA,
        ]);

        $this->assertSame('', $out['error']);
    }

    public function test_rellena_id_nivel_desde_la_asignatura(): void
    {
        $asig = $this->createMock(Asignatura::class);
        $asig->method('getId_nivel')->willReturn(2100);
        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->with(1101)->willReturn($asig);

        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->method('findById')->willReturn(null);
        $mat->expects($this->once())->method('Guardar')->willReturnCallback(
            function ($oMatricula) {
                $this->assertSame('2100', $oMatricula->getId_nivel());

                return true;
            }
        );

        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->method('getActividadAsignaturas')->willReturn([]);

        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->method('getActividadAsignaturas')->willReturn([]);
        $dl->expects($this->once())->method('Guardar')->willReturn(true);

        $out = $this->useCase(
            $asigRepo,
            $dl,
            $all,
            $mat,
            $this->dossiersQueGuardanPersona(),
        )->execute([
            'id_activ' => 10,
            'id_pau' => 7,
            'id_asignatura' => 1101,
        ]);

        $this->assertSame('', $out['error']);
    }

    private function useCase(
        AsignaturaRepositoryInterface $asig,
        ActividadAsignaturaDlRepositoryInterface $dl,
        ActividadAsignaturaRepositoryInterface $all,
        MatriculaDlRepositoryInterface $mat,
        DossierRepositoryInterface $dossiers,
    ): MatriculaNueva {
        return new MatriculaNueva($asig, $dl, $all, $mat, $dossiers);
    }

    private function dossiersQueGuardanPersona(): DossierRepositoryInterface
    {
        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');
        $dossiers = $this->createMock(DossierRepositoryInterface::class);
        $dossiers->method('findByPk')->willReturnOnConsecutiveCalls($dossier, null);
        $dossiers->expects($this->once())->method('Guardar')->with($dossier);

        return $dossiers;
    }
}
