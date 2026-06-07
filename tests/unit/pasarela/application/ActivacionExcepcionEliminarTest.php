<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ActivacionExcepcionEliminar;
use src\pasarela\domain\Activacion;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;

final class ActivacionExcepcionEliminarTest extends TestCase
{
    public function test_falta_id_tipo_activ(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $this->assertNotSame('', (new ActivacionExcepcionEliminar(new Activacion($repo)))->execute(''));
    }

    public function test_elimina_excepcion(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $this->assertSame('', (new ActivacionExcepcionEliminar(new Activacion($repo)))->execute('111000'));
    }
}
