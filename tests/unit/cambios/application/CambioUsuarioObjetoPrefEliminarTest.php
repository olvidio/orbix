<?php

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\cambios\application\CambioUsuarioObjetoPrefEliminar;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;

final class CambioUsuarioObjetoPrefEliminarTest extends TestCase
{
    public function test_sin_id(): void
    {
        $repo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $useCase = new CambioUsuarioObjetoPrefEliminar($repo);

        $rta = $useCase->execute([]);
        $this->assertNotSame('', $rta['error']);
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $useCase = new CambioUsuarioObjetoPrefEliminar($repo);

        $rta = $useCase->execute(['id_item_usuario_objeto' => 5]);
        $this->assertNotSame('', $rta['error']);
    }

    public function test_falla_eliminar(): void
    {
        $pref = $this->createMock(CambioUsuarioObjetoPref::class);

        $repo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $repo->method('findById')->willReturn($pref);
        $repo->method('Eliminar')->with($pref)->willReturn(false);

        $useCase = new CambioUsuarioObjetoPrefEliminar($repo);

        $rta = $useCase->execute(['id_item_usuario_objeto' => 1]);
        $this->assertNotSame('', $rta['error']);
    }

    public function test_exito(): void
    {
        $pref = $this->createMock(CambioUsuarioObjetoPref::class);

        $repo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $repo->method('findById')->willReturn($pref);
        $repo->method('Eliminar')->with($pref)->willReturn(true);

        $useCase = new CambioUsuarioObjetoPrefEliminar($repo);

        $this->assertSame(
            ['error' => ''],
            $useCase->execute(['id_item_usuario_objeto' => 99])
        );
    }
}
