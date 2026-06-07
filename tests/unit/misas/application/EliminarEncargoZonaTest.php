<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\misas\application\EliminarEncargoZona;

final class EliminarEncargoZonaTest extends TestCase
{
    public function test_encargo_no_encontrado(): void
    {
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn(null);

        $msg = (new EliminarEncargoZona($repo))->execute(9);
        $this->assertStringContainsString('9', $msg);
    }

    public function test_falla_eliminar(): void
    {
        $enc = $this->createMock(Encargo::class);
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->willReturn($enc);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db-err');

        $this->assertSame('db-err', (new EliminarEncargoZona($repo))->execute(1));
    }

    public function test_exito(): void
    {
        $enc = $this->createMock(Encargo::class);
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->willReturn($enc);
        $repo->expects($this->once())->method('Eliminar')->with($enc)->willReturn(true);

        $this->assertSame('', (new EliminarEncargoZona($repo))->execute(3));
    }
}
