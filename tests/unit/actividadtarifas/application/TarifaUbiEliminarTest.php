<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TarifaUbiEliminar;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;

final class TarifaUbiEliminarTest extends TestCase
{
    public function test_sin_id_item(): void
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $this->assertNotSame('', (new TarifaUbiEliminar($repo))->execute(['id_item' => 0]));
    }

    public function test_exito(): void
    {
        $o = $this->createMock(TarifaUbi::class);
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn($o);
        $repo->expects($this->once())->method('Eliminar')->with($o)->willReturn(true);
        $repo->method('getErrorTxt')->willReturn('');

        $this->assertSame('', (new TarifaUbiEliminar($repo))->execute(['id_item' => 9]));
    }

    public function test_falla_eliminar_incluye_error_txt(): void
    {
        $o = $this->createMock(TarifaUbi::class);
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('detalle');

        $msg = (new TarifaUbiEliminar($repo))->execute(['id_item' => 1]);
        $this->assertStringContainsString('detalle', $msg);
    }
}
