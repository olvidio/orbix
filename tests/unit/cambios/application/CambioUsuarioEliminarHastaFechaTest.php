<?php

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\cambios\application\CambioUsuarioEliminarHastaFecha;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;

final class CambioUsuarioEliminarHastaFechaTest extends TestCase
{
    public function test_sin_fecha(): void
    {
        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $useCase = new CambioUsuarioEliminarHastaFecha($repo);

        $rta = $useCase->execute([]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_error_si_repositorio_devuelve_false(): void
    {
        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->method('eliminarHastaFecha')->with('2024-01-15')->willReturn(false);

        $useCase = new CambioUsuarioEliminarHastaFecha($repo);

        $rta = $useCase->execute(['f_fin' => '2024-01-15']);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_exito(): void
    {
        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->method('eliminarHastaFecha')->with('2024-01-15')->willReturn(true);

        $useCase = new CambioUsuarioEliminarHastaFecha($repo);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            $useCase->execute(['f_fin' => '2024-01-15'])
        );
    }
}
