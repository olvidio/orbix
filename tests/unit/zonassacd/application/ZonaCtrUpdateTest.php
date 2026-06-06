<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\zonassacd\application\ZonaCtrUpdate;

final class ZonaCtrUpdateTest extends TestCase
{
    public function test_id_ubi_empezando_por_1_usa_CentroDl_y_guarda_zona(): void
    {
        $oCentro = $this->createMock(CentroDl::class);
        $oCentro->expects($this->once())->method('setId_zona')->with(7);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->once())
            ->method('findById')
            ->with(1001)
            ->willReturn($oCentro);
        $centroDlRepo->expects($this->once())
            ->method('Guardar')
            ->with($oCentro)
            ->willReturn(true);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->never())->method('findById');

        $update = new ZonaCtrUpdate($centroDlRepo, $centroEllasRepo);
        $out = $update->execute('7', ['1001']);

        $this->assertSame(['tipo' => 'update', 'mensaje' => '', 'error' => ''], $out);
    }

    public function test_id_ubi_empezando_por_otro_digito_usa_CentroEllas(): void
    {
        $oCentro = $this->createMock(CentroEllas::class);
        $oCentro->expects($this->once())->method('setId_zona')->with(3);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->once())
            ->method('findById')
            ->with(2005)
            ->willReturn($oCentro);
        $centroEllasRepo->expects($this->once())
            ->method('Guardar')
            ->willReturn(true);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->never())->method('findById');

        $update = new ZonaCtrUpdate($centroDlRepo, $centroEllasRepo);
        $out = $update->execute('3', ['2005']);

        $this->assertSame('', $out['mensaje']);
    }

    public function test_id_zona_no_se_normaliza_a_null(): void
    {
        $oCentro = $this->createMock(CentroDl::class);
        $oCentro->expects($this->once())->method('setId_zona')->with(null);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('findById')->willReturn($oCentro);
        $centroDlRepo->method('Guardar')->willReturn(true);

        $update = new ZonaCtrUpdate(
            $centroDlRepo,
            $this->createStub(CentroEllasRepositoryInterface::class),
        );
        $update->execute('no', ['1042']);
    }

    public function test_guardar_false_acumula_error_por_cada_centro_afectado(): void
    {
        $oCentro = $this->createStub(CentroDl::class);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('findById')->willReturn($oCentro);
        $centroDlRepo->method('Guardar')->willReturn(false);

        $update = new ZonaCtrUpdate(
            $centroDlRepo,
            $this->createStub(CentroEllasRepositoryInterface::class),
        );
        $out = $update->execute('9', ['1001', '1002']);

        $this->assertSame("hay un error, no se ha guardado.\nhay un error, no se ha guardado.", $out['mensaje']);
    }
}
