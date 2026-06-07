<?php

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\ColeccionesOpcionesData;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;

final class ColeccionesOpcionesDataTest extends TestCase
{
    public function test_devuelve_opciones_del_repositorio(): void
    {
        $repo = $this->createMock(ColeccionRepositoryInterface::class);
        $repo->method('getArrayColecciones')->willReturn(['1' => 'A', '2' => 'B']);
        $service = new ColeccionesOpcionesData($repo);

        $this->assertSame(
            ['a_opciones' => ['1' => 'A', '2' => 'B']],
            $service->execute()
        );
    }
}
