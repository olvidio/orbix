<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\EliminarEncargoCentro;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;

final class EliminarEncargoCentroTest extends TestCase
{
    private const UUID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_id_vacio(): void
    {
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $this->assertNotSame('', (new EliminarEncargoCentro($repo))->execute(''));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $msg = (new EliminarEncargoCentro($repo))->execute(self::UUID);
        $this->assertStringContainsString(self::UUID, $msg);
    }

    public function test_falla_eliminar(): void
    {
        $ctr = new EncargoCtr();
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn($ctr);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('del-fail');

        $this->assertSame('del-fail', (new EliminarEncargoCentro($repo))->execute(self::UUID));
    }

    public function test_exito(): void
    {
        $ctr = new EncargoCtr();
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn($ctr);
        $repo->expects($this->once())->method('Eliminar')->with($ctr)->willReturn(true);

        $this->assertSame('', (new EliminarEncargoCentro($repo))->execute(self::UUID));
    }
}
