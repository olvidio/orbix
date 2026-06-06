<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\GrupoCasaUpdate;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;

final class GrupoCasaUpdateTest extends TestCase
{
    public function test_faltan_casas(): void
    {
        $useCase = new GrupoCasaUpdate(
            $this->createMock(GrupoCasaRepositoryInterface::class),
        );

        $this->assertNotSame('', $useCase->execute([
            'id_item' => 'nuevo',
            'id_ubi_padre' => 0,
            'id_ubi_hijo' => 3,
        ]));
    }

    public function test_misma_casa(): void
    {
        $useCase = new GrupoCasaUpdate(
            $this->createMock(GrupoCasaRepositoryInterface::class),
        );

        $this->assertNotSame('', $useCase->execute([
            'id_item' => 'nuevo',
            'id_ubi_padre' => 4,
            'id_ubi_hijo' => 4,
        ]));
    }

    public function test_nuevo_getNewId_y_guardar(): void
    {
        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(100);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (GrupoCasa $g) {
            $this->assertSame(100, $g->getId_item());
            $this->assertSame(1, $g->getId_ubi_padre());
            $this->assertSame(2, $g->getId_ubi_hijo());
            return true;
        });

        $this->assertSame('', (new GrupoCasaUpdate($repo))->execute([
            'id_item' => 'nuevo',
            'id_ubi_padre' => 1,
            'id_ubi_hijo' => 2,
        ]));
    }

    public function test_edita_grupo_no_encontrado(): void
    {
        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn(null);

        $this->assertNotSame('', (new GrupoCasaUpdate($repo))->execute([
            'id_item' => '7',
            'id_ubi_padre' => 1,
            'id_ubi_hijo' => 2,
        ]));
    }

    public function test_falla_guardar(): void
    {
        $existente = new GrupoCasa();
        $existente->setId_item(7);

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->willReturn($existente);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $msg = (new GrupoCasaUpdate($repo))->execute([
            'id_item' => '7',
            'id_ubi_padre' => 3,
            'id_ubi_hijo' => 4,
        ]);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('db', $msg);
    }

    public function test_exito_edicion(): void
    {
        $existente = new GrupoCasa();
        $existente->setId_item(7);

        $repo = $this->createMock(GrupoCasaRepositoryInterface::class);
        $repo->method('findById')->willReturn($existente);
        $repo->method('Guardar')->willReturn(true);

        $this->assertSame('', (new GrupoCasaUpdate($repo))->execute([
            'id_item' => '7',
            'id_ubi_padre' => 3,
            'id_ubi_hijo' => 4,
        ]));
        $this->assertSame(3, $existente->getId_ubi_padre());
        $this->assertSame(4, $existente->getId_ubi_hijo());
    }
}
