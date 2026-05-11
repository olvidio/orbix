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
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_sin_id_item_devuelve_mensaje_error(): void
    {
        $msg = (new ProcesosEliminar())->execute([]);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_id_item_cero_devuelve_mensaje_error(): void
    {
        $msg = (new ProcesosEliminar())->execute(['id_item' => 0]);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_id_item_negativo_devuelve_mensaje_error(): void
    {
        $msg = (new ProcesosEliminar())->execute(['id_item' => -3]);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_id_item_no_numerico_se_trata_como_cero(): void
    {
        $msg = (new ProcesosEliminar())->execute(['id_item' => 'abc']);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_tarea_no_encontrada(): void
    {
        $repo = $this->createMock(TareaProcesoRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            TareaProcesoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            _('no se encuentra la tarea a borrar'),
            (new ProcesosEliminar())->execute(['id_item' => 5])
        );
    }

    public function test_falla_eliminar(): void
    {
        $t = $this->createMock(TareaProceso::class);
        $repo = $this->createMock(TareaProcesoRepositoryInterface::class);
        $repo->method('findById')->willReturn($t);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $GLOBALS['container'] = $this->containerFromMap([
            TareaProcesoRepositoryInterface::class => $repo,
        ]);

        $msg = (new ProcesosEliminar())->execute(['id_item' => 1]);
        $this->assertStringContainsString('db', $msg);
    }

    public function test_exito(): void
    {
        $t = $this->createMock(TareaProceso::class);
        $repo = $this->createMock(TareaProcesoRepositoryInterface::class);
        $repo->method('findById')->willReturn($t);
        $repo->expects($this->once())->method('Eliminar')->with($t)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            TareaProcesoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', (new ProcesosEliminar())->execute(['id_item' => 99]));
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
