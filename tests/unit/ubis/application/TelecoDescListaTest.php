<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\TelecoDescLista;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

final class TelecoDescListaTest extends TestCase
{
    public function test_envuelve_opciones_del_repositorio(): void
    {
        $repo = $this->createMock(DescTelecoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayDescTelecoUbis')
            ->with(3)
            ->willReturn(['a' => 'A', 'b' => 'B']);

        $useCase = new TelecoDescLista($repo);

        $this->assertSame(['a_desc' => ['a' => 'A', 'b' => 'B']], $useCase->execute(3));
    }
}
