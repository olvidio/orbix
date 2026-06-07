<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ActivacionExcepcionGuardar;
use src\pasarela\domain\Activacion;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;

final class ActivacionExcepcionGuardarTest extends TestCase
{
    public function test_falta_id_tipo_activ(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $this->assertNotSame('', (new ActivacionExcepcionGuardar(new Activacion($repo)))->execute('', 'x'));
    }

    public function test_falta_valor(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $this->assertNotSame('', (new ActivacionExcepcionGuardar(new Activacion($repo)))->execute('111000', ''));
    }

    public function test_guarda_excepcion(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $this->assertSame('', (new ActivacionExcepcionGuardar(new Activacion($repo)))->execute('111000', '2 días'));
    }
}
