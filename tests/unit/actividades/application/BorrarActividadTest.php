<?php

namespace Tests\unit\actividades\application;

use src\actividades\application\BorrarActividad;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\Importada;
use src\actividades\domain\value_objects\StatusId;
use Tests\myTest;

/**
 * `BorrarActividad` usa la delegación propia de la sesión de test (`H-dlbv` → mi_dele `dlb`).
 */
class BorrarActividadTest extends myTest
{
    public function test_dl_propia_proyecto_eliminar_ok_devuelve_vacio(): void
    {
        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getDl_org')->willReturn('dlb');
        $actividad->method('getId_tabla')->willReturn('ex');
        $actividad->method('getStatus')->willReturn(StatusId::PROYECTO);

        $allRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepo->method('findById')->with(10)->willReturn($actividad);

        $dlRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $dlRepo->expects($this->once())->method('Eliminar')->with($actividad)->willReturn(true);

        $useCase = new BorrarActividad($allRepo, $dlRepo, $this->createMock(ActividadExRepositoryInterface::class), $this->createMock(ImportadaRepositoryInterface::class));

        $this->assertSame('', $useCase->ejecutar(10));
    }

    public function test_dl_propia_proyecto_eliminar_falla_devuelve_mensaje(): void
    {
        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getDl_org')->willReturn('dlb');
        $actividad->method('getId_tabla')->willReturn('ex');
        $actividad->method('getStatus')->willReturn(StatusId::PROYECTO);

        $allRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepo->method('findById')->willReturn($actividad);

        $dlRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $dlRepo->method('Eliminar')->willReturn(false);
        $dlRepo->method('getErrorTxt')->willReturn('detalle');

        $useCase = new BorrarActividad($allRepo, $dlRepo, $this->createMock(ActividadExRepositoryInterface::class), $this->createMock(ImportadaRepositoryInterface::class));

        $out = $useCase->ejecutar(1);
        $this->assertStringContainsString('no se ha eliminado', $out);
        $this->assertStringContainsString('detalle', $out);
    }

    public function test_dl_propia_no_proyecto_marca_borrable_y_guarda(): void
    {
        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getDl_org')->willReturn('dlb');
        $actividad->method('getId_tabla')->willReturn('ex');
        $actividad->method('getStatus')->willReturn(StatusId::ACTUAL);
        $actividad->expects($this->once())->method('setStatus')->with(StatusId::BORRABLE);

        $allRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepo->method('findById')->willReturn($actividad);

        $dlRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $dlRepo->expects($this->once())->method('Guardar')->with($actividad)->willReturn(true);

        $useCase = new BorrarActividad($allRepo, $dlRepo, $this->createMock(ActividadExRepositoryInterface::class), $this->createMock(ImportadaRepositoryInterface::class));

        $this->assertSame('', $useCase->ejecutar(2));
    }

    public function test_otra_dl_importada_elimina_importada(): void
    {
        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getDl_org')->willReturn('zzz');
        $actividad->method('getId_tabla')->willReturn('dl');

        $importada = $this->createMock(Importada::class);

        $allRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepo->method('findById')->willReturn($actividad);

        $impRepo = $this->createMock(ImportadaRepositoryInterface::class);
        $impRepo->expects($this->once())->method('findById')->with(3)->willReturn($importada);
        $impRepo->expects($this->once())->method('Eliminar')->with($importada);

        $useCase = new BorrarActividad($allRepo, $this->createMock(ActividadDlRepositoryInterface::class), $this->createMock(ActividadExRepositoryInterface::class), $impRepo);

        $this->assertSame('', $useCase->ejecutar(3));
    }

    public function test_otra_dl_ex_marca_borrable_en_repositorio_ex(): void
    {
        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getDl_org')->willReturn('zzz');
        $actividad->method('getId_tabla')->willReturn('ex');
        $actividad->expects($this->once())->method('setStatus')->with(StatusId::BORRABLE);

        $allRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepo->method('findById')->willReturn($actividad);

        $exRepo = $this->createMock(ActividadExRepositoryInterface::class);
        $exRepo->expects($this->once())->method('Guardar')->with($actividad)->willReturn(true);

        $useCase = new BorrarActividad($allRepo, $this->createMock(ActividadDlRepositoryInterface::class), $exRepo, $this->createMock(ImportadaRepositoryInterface::class));

        $this->assertSame('', $useCase->ejecutar(4));
    }
}
