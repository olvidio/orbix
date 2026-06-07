<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoVerEliminar;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;

final class EncargoVerEliminarTest extends TestCase
{
    public function test_sel_vacio_devuelve_sin_error(): void
    {
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $useCase = new EncargoVerEliminar($repo);

        $this->assertSame(['error' => ''], $useCase->execute([]));
        $this->assertSame(['error' => ''], $useCase->execute(['sel' => []]));
    }

    public function test_encargo_no_encontrado(): void
    {
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->with(12)->willReturn(null);
        $useCase = new EncargoVerEliminar($repo);

        $rta = $useCase->execute(['sel' => ['12#extra']]);
        $this->assertNotSame('', $rta['error']);
    }

    public function test_falla_eliminar(): void
    {
        $enc = $this->createMock(Encargo::class);

        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->willReturn($enc);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');
        $useCase = new EncargoVerEliminar($repo);

        $rta = $useCase->execute(['sel' => ['5#']]);
        $this->assertNotSame('', $rta['error']);
        $this->assertStringContainsString('db', $rta['error']);
    }

    public function test_exito(): void
    {
        $enc = $this->createMock(Encargo::class);

        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->with(99)->willReturn($enc);
        $repo->method('Eliminar')->with($enc)->willReturn(true);
        $useCase = new EncargoVerEliminar($repo);

        $this->assertSame(['error' => ''], $useCase->execute(['sel' => ['99#x']]));
    }
}
