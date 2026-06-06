<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TipoTarifaEliminar;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;

final class TipoTarifaEliminarTest extends TestCase
{
    public function test_sin_id(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $this->assertNotSame('', (new TipoTarifaEliminar($repo))->execute(['id_tarifa' => 0]));
    }

    public function test_no_encontrada(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $this->assertNotSame('', (new TipoTarifaEliminar($repo))->execute(['id_tarifa' => 5]));
    }

    public function test_exito(): void
    {
        $o = $this->createMock(TipoTarifa::class);
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->expects($this->once())->method('Eliminar')->with($o)->willReturn(true);

        $this->assertSame('', (new TipoTarifaEliminar($repo))->execute(['id_tarifa' => 5]));
    }

    public function test_falla_eliminar(): void
    {
        $o = $this->createMock(TipoTarifa::class);
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->method('Eliminar')->willReturn(false);

        $this->assertNotSame('', (new TipoTarifaEliminar($repo))->execute(['id_tarifa' => 5]));
    }
}
