<?php

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\EquipajeEliminar;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;

final class EquipajeEliminarTest extends TestCase
{
    public function test_id_invalido(): void
    {
        $repo = $this->createMock(EquipajeRepositoryInterface::class);
        $service = new EquipajeEliminar($repo);

        $this->assertNotSame('', $service->execute(0));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(EquipajeRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn(null);

        $service = new EquipajeEliminar($repo);

        $this->assertNotSame('', $service->execute(9));
    }

    public function test_falla_eliminar(): void
    {
        $eq = $this->createMock(Equipaje::class);

        $repo = $this->createMock(EquipajeRepositoryInterface::class);
        $repo->method('findById')->willReturn($eq);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $service = new EquipajeEliminar($repo);

        $msg = $service->execute(3);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('db', $msg);
    }

    public function test_exito(): void
    {
        $eq = $this->createMock(Equipaje::class);

        $repo = $this->createMock(EquipajeRepositoryInterface::class);
        $repo->method('findById')->with(1)->willReturn($eq);
        $repo->method('Eliminar')->with($eq)->willReturn(true);

        $service = new EquipajeEliminar($repo);

        $this->assertSame('', $service->execute(1));
    }
}
