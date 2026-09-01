<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\ActividadAsignaturaNueva;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;

final class ActividadAsignaturaNuevaTest extends TestCase
{
    public function test_faltan_claves_no_guarda(): void
    {
        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->expects($this->never())->method('Guardar');
        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->expects($this->never())->method('getActividadAsignaturas');
        $dossiers = $this->createMock(DossierRepositoryInterface::class);

        $out = (new ActividadAsignaturaNueva($dl, $all, $dossiers))->execute([
            'id_activ' => 0,
            'id_asignatura' => 1101,
        ]);

        $this->assertFalse($out['requiere_confirmacion']);
        $this->assertNotSame('', $out['error']);
    }

    public function test_si_ya_existe_pide_confirmacion_y_no_guarda(): void
    {
        $existente = new ActividadAsignatura();
        $existente->setId_schema(2002);
        $existente->setId_activ(10);
        $existente->setIdAsignaturaVo(1101);

        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->expects($this->never())->method('Guardar');

        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->expects($this->once())->method('getActividadAsignaturas')->with([
            'id_activ' => 10,
            'id_asignatura' => 1101,
        ])->willReturn([$existente]);

        $dossiers = $this->createMock(DossierRepositoryInterface::class);
        $dossiers->expects($this->never())->method('Guardar');

        $out = (new ActividadAsignaturaNueva($dl, $all, $dossiers))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
            'id_profesor' => 5,
        ]);

        $this->assertTrue($out['requiere_confirmacion']);
        $this->assertSame('', $out['error']);
        $this->assertSame(ActividadAsignaturaNueva::mensajeDuplicado(), $out['mensaje']);
    }

    public function test_con_confirmacion_guarda_aunque_exista(): void
    {
        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->expects($this->once())->method('Guardar')->willReturn(true);

        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->expects($this->never())->method('getActividadAsignaturas');

        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');
        $dossiers = $this->createMock(DossierRepositoryInterface::class);
        $dossiers->method('findByPk')->willReturn($dossier);
        $dossiers->expects($this->once())->method('Guardar')->with($dossier);

        $out = (new ActividadAsignaturaNueva($dl, $all, $dossiers))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
            'id_profesor' => 5,
            'confirmar_duplicado' => '1',
        ]);

        $this->assertFalse($out['requiere_confirmacion']);
        $this->assertSame('', $out['error']);
    }

    public function test_nueva_sin_duplicado_guarda(): void
    {
        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->expects($this->once())->method('Guardar')->willReturn(true);

        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->method('getActividadAsignaturas')->willReturn([]);

        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');
        $dossiers = $this->createMock(DossierRepositoryInterface::class);
        $dossiers->method('findByPk')->willReturn($dossier);
        $dossiers->expects($this->once())->method('Guardar')->with($dossier);

        $out = (new ActividadAsignaturaNueva($dl, $all, $dossiers))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
        ]);

        $this->assertFalse($out['requiere_confirmacion']);
        $this->assertSame('', $out['error']);
    }

    public function test_guardar_falla_devuelve_error(): void
    {
        $dl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $dl->method('Guardar')->willReturn(false);
        $all = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $all->method('getActividadAsignaturas')->willReturn([]);
        $dossiers = $this->createMock(DossierRepositoryInterface::class);
        $dossiers->expects($this->never())->method('Guardar');

        $out = (new ActividadAsignaturaNueva($dl, $all, $dossiers))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
        ]);

        $this->assertStringContainsString('no se ha creado', $out['error']);
    }
}
