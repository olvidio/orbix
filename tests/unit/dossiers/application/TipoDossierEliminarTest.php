<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\application\TipoDossierEliminar;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;

final class TipoDossierEliminarTest extends TestCase
{
    public function test_sin_id(): void
    {
        $useCase = new TipoDossierEliminar($this->createMock(TipoDossierRepositoryInterface::class));
        $this->assertNotSame('', $useCase->execute([]));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $useCase = new TipoDossierEliminar($repo);
        $this->assertNotSame('', $useCase->execute(['id_tipo_dossier' => 5]));
    }

    public function test_falla_eliminar(): void
    {
        $tipo = $this->createMock(TipoDossier::class);

        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->willReturn($tipo);
        $repo->method('Eliminar')->willReturn(false);

        $useCase = new TipoDossierEliminar($repo);
        $this->assertNotSame('', $useCase->execute(['id_tipo_dossier' => 1]));
    }

    public function test_exito(): void
    {
        $tipo = $this->createMock(TipoDossier::class);

        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('findById')->willReturn($tipo);
        $repo->method('Eliminar')->willReturn(true);

        $useCase = new TipoDossierEliminar($repo);
        $this->assertSame('', $useCase->execute(['id_tipo_dossier' => 9]));
    }
}
