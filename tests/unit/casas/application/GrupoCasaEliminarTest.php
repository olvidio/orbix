<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\GrupoCasaEliminar;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;

final class GrupoCasaEliminarTest extends TestCase
{
    public function test_sin_id_item(): void
    {
        $useCase = new GrupoCasaEliminar(
            $this->createMock(GrupoCasaRepositoryInterface::class),
        );

        $this->assertNotSame('', $useCase->execute([]));
    }

    public function test_grupo_no_encontrado(): void
    {
        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn(null);

        $this->assertNotSame('', (new GrupoCasaEliminar($repo))->execute(['id_item' => 3]));
    }

    public function test_falla_eliminar(): void
    {
        $grupo = $this->createMock(GrupoCasa::class);

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->willReturn($grupo);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('err db');

        $msg = (new GrupoCasaEliminar($repo))->execute(['id_item' => 1]);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('err db', $msg);
    }

    public function test_exito(): void
    {
        $grupo = $this->createMock(GrupoCasa::class);

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->willReturn($grupo);
        $repo->method('Eliminar')->willReturn(true);

        $this->assertSame('', (new GrupoCasaEliminar($repo))->execute(['id_item' => 9]));
    }
}
