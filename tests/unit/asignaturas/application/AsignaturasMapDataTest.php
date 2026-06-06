<?php

declare(strict_types=1);

namespace Tests\unit\asignaturas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\application\AsignaturasMapData;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

final class AsignaturasMapDataTest extends TestCase
{
    public function test_execute_devuelve_mapa_de_asignaturas(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayAsignaturas')
            ->willReturn([1001 => 'MAT', 2001 => 'FIS']);

        $useCase = new AsignaturasMapData($repo);

        $this->assertSame([
            'a_asignaturas' => [1001 => 'MAT', 2001 => 'FIS'],
        ], $useCase->execute());
    }
}
