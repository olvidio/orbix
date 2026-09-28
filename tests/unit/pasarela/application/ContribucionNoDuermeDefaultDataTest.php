<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ContribucionNoDuermeDefaultData;
use src\pasarela\domain\ContribucionNoDuerme;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\shared\security\HashB;

final class ContribucionNoDuermeDefaultDataTest extends TestCase
{
    public function test_emite_ctx_guardar_sin_contexto(): void
    {
        $pasRepo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $pasRepo->method('findById')->willReturn(null);

        $out = (new ContribucionNoDuermeDefaultData(new ContribucionNoDuerme($pasRepo)))->execute();

        $this->assertArrayHasKey('default', $out);
        $this->assertSame([], HashB::open($out['ctx_guardar'], 'contribucion_no_duerme_default_guardar'));
    }
}
