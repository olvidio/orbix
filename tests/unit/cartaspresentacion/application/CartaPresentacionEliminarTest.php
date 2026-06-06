<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartaPresentacionEliminar;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;

final class CartaPresentacionEliminarTest extends TestCase
{
    public function test_faltan_ids(): void
    {
        $useCase = new CartaPresentacionEliminar(
            $this->createMock(CartaPresentacionRepositoryInterface::class),
        );

        $rta = $useCase->execute([]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_no_encontrada(): void
    {
        $repo = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $repo->method('findById')->with(3, 4)->willReturn(null);

        $rta = (new CartaPresentacionEliminar($repo))->execute(['id_ubi' => 3, 'id_direccion' => 4]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_falla_eliminar(): void
    {
        $carta = $this->createMock(CartaPresentacion::class);

        $repo = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $repo->method('findById')->willReturn($carta);
        $repo->method('Eliminar')->with($carta)->willReturn(false);

        $rta = (new CartaPresentacionEliminar($repo))->execute(['id_ubi' => 1, 'id_direccion' => 2]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_exito(): void
    {
        $carta = $this->createMock(CartaPresentacion::class);

        $repo = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $repo->method('findById')->willReturn($carta);
        $repo->method('Eliminar')->with($carta)->willReturn(true);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            (new CartaPresentacionEliminar($repo))->execute(['id_ubi' => 9, 'id_direccion' => 8])
        );
    }
}
