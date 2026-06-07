<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosSelectData;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;

final class ProcesosSelectDataTest extends TestCase
{
    public function test_devuelve_array_tipos_desde_repositorio(): void
    {
        $repo = $this->createMock(ProcesoTipoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayProcesoTipos')
            ->willReturn([1 => 'Tipo A', 2 => 'Tipo B']);

        $useCase = new ProcesosSelectData($repo);
        $out = $useCase->execute();
        $this->assertSame([1 => 'Tipo A', 2 => 'Tipo B'], $out['a_tipos_proceso']);
    }
}
