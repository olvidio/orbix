<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TarifaUbiUpdate;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;

final class TarifaUbiUpdateTest extends TestCase
{
    public function test_no_encontrada(): void
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->with(10)->willReturn(null);

        $this->assertNotSame('', (new TarifaUbiUpdate($repo))->execute(['id_item' => 10]));
    }

    public function test_actualizar_exito(): void
    {
        $o = $this->createMock(TarifaUbi::class);
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->with(10)->willReturn($o);
        $o->expects($this->once())->method('setCantidad')->with(12.5);
        $repo->expects($this->once())->method('Guardar')->with($o)->willReturn(true);
        $repo->method('getErrorTxt')->willReturn('');

        $this->assertSame('', (new TarifaUbiUpdate($repo))->execute([
            'id_item' => 10,
            'cantidad' => '12.5',
        ]));
    }

    public function test_guardar_falla(): void
    {
        $o = $this->createMock(TarifaUbi::class);
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $msg = (new TarifaUbiUpdate($repo))->execute(['id_item' => 1]);
        $this->assertStringContainsString('db', $msg);
    }
}
