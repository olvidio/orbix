<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\NombreExcepcionEliminar;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\Nombre;

final class NombreExcepcionEliminarTest extends TestCase
{
    public function test_falta_id_tipo_activ(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $this->assertNotSame('', (new NombreExcepcionEliminar(new Nombre($repo)))->execute(''));
    }

    public function test_elimina_excepcion(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $this->assertSame('', (new NombreExcepcionEliminar(new Nombre($repo)))->execute('111000'));
    }
}
