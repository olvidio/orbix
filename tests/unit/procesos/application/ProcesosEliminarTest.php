<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosEliminar;

/**
 * Test unitario para las guardas de ProcesosEliminar (id_item <= 0),
 * sin necesidad de BD.
 */
final class ProcesosEliminarTest extends TestCase
{
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
}
