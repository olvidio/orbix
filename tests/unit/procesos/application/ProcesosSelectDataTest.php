<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosSelectData;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\shared\security\HashB;

final class ProcesosSelectDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        session_id('test-session-procesos-select');
    }

    public function test_devuelve_array_tipos_desde_repositorio(): void
    {
        $repo = $this->createMock(ProcesoTipoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayProcesoTipos')
            ->willReturn([1 => 'Tipo A', 2 => 'Tipo B']);

        $useCase = new ProcesosSelectData($repo);
        $out = $useCase->execute();
        $this->assertSame([1 => 'Tipo A', 2 => 'Tipo B'], $out['a_tipos_proceso']);
        $this->assertSame(['id_tipo_proceso' => 1], HashB::open($out['ctx_regenerar'][1], 'procesos_regenerar'));
        $this->assertSame(['id_tipo_proceso' => 2], HashB::open($out['ctx_clonar'][2], 'procesos_clonar'));
    }
}
