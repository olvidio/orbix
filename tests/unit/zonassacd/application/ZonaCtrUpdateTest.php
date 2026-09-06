<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\zonassacd\application\ZonaCtrUpdate;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

final class ZonaCtrUpdateTest extends TestCase
{
    public function test_escribe_en_zonas_ctr_y_en_centro_dl(): void
    {
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->expects($this->once())->method('asignar')->with(1001, 7)->willReturn(true);

        $oCentro = $this->createMock(CentroDl::class);
        $oCentro->expects($this->once())->method('setId_zona')->with(7);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->once())->method('findById')->with(1001)->willReturn($oCentro);
        $centroDlRepo->expects($this->once())->method('Guardar')->with($oCentro)->willReturn(true);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->never())->method('findById');

        $out = (new ZonaCtrUpdate($zonaCtr, $centroDlRepo, $centroEllasRepo))->execute('7', ['1001']);
        $this->assertSame(['tipo' => 'update', 'mensaje' => '', 'error' => ''], $out);
    }

    public function test_id_ubi_de_sf_usa_centro_ellas(): void
    {
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->method('asignar')->willReturn(true);

        $oCentro = $this->createMock(CentroEllas::class);
        $oCentro->expects($this->once())->method('setId_zona')->with(3);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->once())->method('findById')->with(2005)->willReturn($oCentro);
        $centroEllasRepo->method('Guardar')->willReturn(true);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->never())->method('findById');

        $out = (new ZonaCtrUpdate($zonaCtr, $centroDlRepo, $centroEllasRepo))->execute('3', ['2005']);
        $this->assertSame('', $out['mensaje']);
    }

    public function test_id_zona_no_se_normaliza_a_null(): void
    {
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->expects($this->once())->method('asignar')->with(1042, null)->willReturn(true);

        $oCentro = $this->createMock(CentroDl::class);
        $oCentro->expects($this->once())->method('setId_zona')->with(null);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('findById')->willReturn($oCentro);
        $centroDlRepo->method('Guardar')->willReturn(true);

        (new ZonaCtrUpdate(
            $zonaCtr,
            $centroDlRepo,
            $this->createStub(CentroEllasRepositoryInterface::class),
        ))->execute('no', ['1042']);
    }

    public function test_fallo_en_zonas_ctr_o_columna_acumula_error(): void
    {
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->method('asignar')->willReturn(false);

        $oCentro = $this->createStub(CentroDl::class);
        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('findById')->willReturn($oCentro);
        $centroDlRepo->method('Guardar')->willReturn(false);

        $out = (new ZonaCtrUpdate(
            $zonaCtr,
            $centroDlRepo,
            $this->createStub(CentroEllasRepositoryInterface::class),
        ))->execute('9', ['1001']);

        $this->assertSame("hay un error, no se ha guardado.\nhay un error, no se ha guardado.", $out['mensaje']);
    }
}
