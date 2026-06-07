<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\DelegacionesRegionStgrData;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

final class DelegacionesRegionStgrDataTest extends TestCase
{
    public function test_delega_al_repositorio(): void
    {
        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayDlRegionStgr')
            ->with(['reg1'])
            ->willReturn([10 => 'DL A', 11 => 'DL B']);

        $useCase = new DelegacionesRegionStgrData($repo);
        $out = $useCase->execute('reg1');

        $this->assertSame([10 => 'DL A', 11 => 'DL B'], $out['a_delegaciones']);
    }
}
