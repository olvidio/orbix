<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ContribucionReservaDefaultData;
use src\pasarela\domain\ContribucionReserva;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\shared\security\HashB;

final class ContribucionReservaDefaultDataTest extends TestCase
{
    public function test_emite_ctx_guardar_sin_contexto(): void
    {
        $pasRepo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $pasRepo->method('findById')->willReturn(null);

        $out = (new ContribucionReservaDefaultData(new ContribucionReserva($pasRepo)))->execute();

        $this->assertArrayHasKey('default', $out);
        $this->assertSame([], HashB::open($out['ctx_guardar'], 'contribucion_reserva_default_guardar'));
    }
}
