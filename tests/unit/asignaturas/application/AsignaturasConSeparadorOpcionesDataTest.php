<?php

declare(strict_types=1);

namespace Tests\unit\asignaturas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\application\AsignaturasConSeparadorOpcionesData;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

final class AsignaturasConSeparadorOpcionesDataTest extends TestCase
{
    public function test_execute_con_genericas_por_defecto(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayAsignaturasConSeparador')
            ->with(true)
            ->willReturn([1001 => 'MAT', 3000 => '----------', 3101 => 'OPC']);

        $useCase = new AsignaturasConSeparadorOpcionesData($repo);

        $this->assertSame([
            'a_opciones' => [1001 => 'MAT', 3000 => '----------', 3101 => 'OPC'],
        ], $useCase->execute());
    }

    public function test_execute_sin_genericas(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayAsignaturasConSeparador')
            ->with(false)
            ->willReturn([1001 => 'MAT']);

        $useCase = new AsignaturasConSeparadorOpcionesData($repo);

        $this->assertSame(['a_opciones' => [1001 => 'MAT']], $useCase->execute(false));
    }
}
