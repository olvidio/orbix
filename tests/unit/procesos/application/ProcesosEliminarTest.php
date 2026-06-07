<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosEliminar;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\TareaProceso;

/**
 * Test unitario para las guardas de ProcesosEliminar (id_item <= 0),
 * sin necesidad de BD.
 */
final class ProcesosEliminarTest extends TestCase
{
    public function test_sin_id_item_devuelve_mensaje_error(): void
    {
        $useCase = new ProcesosEliminar($this->createMock(TareaProcesoRepositoryInterface::class));
        $msg = $useCase->execute([]);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_id_item_cero_devuelve_mensaje_error(): void
    {
        $useCase = new ProcesosEliminar($this->createMock(TareaProcesoRepositoryInterface::class));
        $msg = $useCase->execute(['id_item' => 0]);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_id_item_negativo_devuelve_mensaje_error(): void
    {
        $useCase = new ProcesosEliminar($this->createMock(TareaProcesoRepositoryInterface::class));
        $msg = $useCase->execute(['id_item' => -3]);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_id_item_no_numerico_se_trata_como_cero(): void
    {
        $useCase = new ProcesosEliminar($this->createMock(TareaProcesoRepositoryInterface::class));
        $msg = $useCase->execute(['id_item' => 'abc']);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_tarea_no_encontrada(): void
    {
        $repo = $this->createMock(TareaProcesoRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $this->assertSame(
            _('no se encuentra la tarea a borrar'),
            (new ProcesosEliminar($repo))->execute(['id_item' => 5])
        );
    }

    public function test_falla_eliminar(): void
    {
        $t = $this->createMock(TareaProceso::class);
        $repo = $this->createMock(TareaProcesoRepositoryInterface::class);
        $repo->method('findById')->willReturn($t);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $msg = (new ProcesosEliminar($repo))->execute(['id_item' => 1]);
        $this->assertStringContainsString('db', $msg);
    }

    public function test_exito(): void
    {
        $t = $this->createMock(TareaProceso::class);
        $repo = $this->createMock(TareaProcesoRepositoryInterface::class);
        $repo->method('findById')->willReturn($t);
        $repo->expects($this->once())->method('Eliminar')->with($t)->willReturn(true);

        $this->assertSame('', (new ProcesosEliminar($repo))->execute(['id_item' => 99]));
    }
}
