<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\ActaPdfEliminar;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;

final class ActaPdfEliminarTest extends TestCase
{
    public function test_acta_vacia(): void
    {
        $useCase = new ActaPdfEliminar($this->createMock(ActaRepositoryInterface::class));
        $this->assertNotSame('', $useCase->execute([]));
    }

    public function test_acta_no_encontrada(): void
    {
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->with('42')->willReturn(null);

        $useCase = new ActaPdfEliminar($repo);
        $this->assertNotSame('', $useCase->execute(['acta_num' => '42']));
    }

    public function test_falla_guardar(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->expects($this->once())->method('setPdf')->with('');

        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn($acta);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('pdf-del-fail');

        $useCase = new ActaPdfEliminar($repo);
        $this->assertSame('pdf-del-fail', $useCase->execute(['acta_num' => '1']));
    }

    public function test_exito(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->expects($this->once())->method('setPdf')->with('');

        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn($acta);
        $repo->expects($this->once())->method('Guardar')->with($acta)->willReturn(true);

        $useCase = new ActaPdfEliminar($repo);
        $this->assertSame('', $useCase->execute(['acta_num' => '9']));
    }
}
