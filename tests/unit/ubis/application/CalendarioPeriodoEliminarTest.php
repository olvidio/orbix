<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CalendarioPeriodoEliminar;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CalendarioPeriodoEliminarTest extends TestCase
{
    public function test_id_cero_devuelve_mensaje_error(): void
    {
        $useCase = new CalendarioPeriodoEliminar($this->createMock(CasaPeriodoRepositoryInterface::class));
        $this->assertSame(_('no sé cuál he de borar'), $useCase->execute(0));
    }

    public function test_id_negativo_devuelve_mensaje_error(): void
    {
        $useCase = new CalendarioPeriodoEliminar($this->createMock(CasaPeriodoRepositoryInterface::class));
        $this->assertSame(_('no sé cuál he de borar'), $useCase->execute(-5));
    }
}
