<?php

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\TipoDocOpcionesData;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;

final class TipoDocOpcionesDataTest extends TestCase
{
    public function test_devuelve_opciones_del_repositorio(): void
    {
        $repo = $this->createMock(TipoDocRepositoryInterface::class);
        $repo->method('getArrayTipoDoc')->willReturn(['x' => 'Tipo X']);
        $service = new TipoDocOpcionesData($repo);

        $this->assertSame(
            ['a_opciones' => ['x' => 'Tipo X']],
            $service->execute()
        );
    }
}
