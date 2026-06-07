<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\ZonaSacdDatosPut;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;

final class ZonaSacdDatosPutTest extends TestCase
{
    public function test_no_existe(): void
    {
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([]);

        $out = (new ZonaSacdDatosPut($repo))->execute(1, 2, []);
        $this->assertNotSame('', $out['error']);
    }

    public function test_falla_guardar(): void
    {
        $zs = $this->createMock(ZonaSacd::class);
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([$zs]);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('put-fail');

        $this->assertSame('put-fail', (new ZonaSacdDatosPut($repo))->execute(1, 2, ['dw1' => true])['error']);
    }

    public function test_exito(): void
    {
        $zs = $this->createMock(ZonaSacd::class);
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([$zs]);
        $repo->expects($this->once())->method('Guardar')->with($zs)->willReturn(true);

        $out = (new ZonaSacdDatosPut($repo))->execute(3, 4, ['dw1' => 'true', 'dw2' => false]);
        $this->assertSame('', $out['error']);
    }
}
