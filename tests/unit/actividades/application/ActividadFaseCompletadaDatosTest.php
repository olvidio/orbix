<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadFaseCompletadaDatos;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

final class ActividadFaseCompletadaDatosTest extends TestCase
{
    public function test_ids_no_positivos_devuelve_false_sin_repo(): void
    {
        $repo = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $repo->expects($this->never())->method('faseCompletada');
        $useCase = new ActividadFaseCompletadaDatos($repo);

        $this->assertSame(['completada' => false], $useCase->ejecutar(0, 1));
        $this->assertSame(['completada' => false], $useCase->ejecutar(1, 0));
    }

    public function test_delega_en_repositorio(): void
    {
        $repo = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('faseCompletada')
            ->with(10, 3)
            ->willReturn(true);

        $out = (new ActividadFaseCompletadaDatos($repo))->ejecutar(10, 3);

        $this->assertSame(['completada' => true], $out);
    }
}
