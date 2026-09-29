<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ActivacionDefaultData;
use src\pasarela\domain\Activacion;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\shared\security\HashB;

final class ActivacionDefaultDataTest extends TestCase
{
    public function test_devuelve_default(): void
    {
        $pasRepo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $pasRepo->method('findById')->willReturn(null);

        $out = (new ActivacionDefaultData(new Activacion($pasRepo)))->execute();
        $this->assertSame('3 días', $out['default']);
    }

    public function test_emite_ctx_guardar_sin_contexto(): void
    {
        $pasRepo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $pasRepo->method('findById')->willReturn(null);

        $out = (new ActivacionDefaultData(new Activacion($pasRepo)))->execute();

        $this->assertSame([], HashB::open($out['ctx_guardar'], 'activacion_default_guardar'));
    }
}
