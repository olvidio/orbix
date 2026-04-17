<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CalendarioPeriodoEliminar;

final class CalendarioPeriodoEliminarTest extends TestCase
{
    public function test_id_cero_devuelve_mensaje_error(): void
    {
        $this->assertSame(_('no sé cuál he de borar'), CalendarioPeriodoEliminar::execute(0));
    }

    public function test_id_negativo_devuelve_mensaje_error(): void
    {
        $this->assertSame(_('no sé cuál he de borar'), CalendarioPeriodoEliminar::execute(-5));
    }
}
