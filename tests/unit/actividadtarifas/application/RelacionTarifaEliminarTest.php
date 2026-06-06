<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\RelacionTarifaEliminar;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;

final class RelacionTarifaEliminarTest extends TestCase
{
    public function test_sin_id_item(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $this->assertNotSame('', (new RelacionTarifaEliminar($repo))->execute(['id_item' => 0]));
    }

    public function test_exito(): void
    {
        $o = $this->createMock(RelacionTarifaTipoActividad::class);
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn($o);
        $repo->expects($this->once())->method('Eliminar')->with($o)->willReturn(true);
        $repo->method('getErrorTxt')->willReturn('');

        $this->assertSame('', (new RelacionTarifaEliminar($repo))->execute(['id_item' => 3]));
    }
}
