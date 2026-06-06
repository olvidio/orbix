<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadFasesCompletadasDatos;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

final class ActividadFasesCompletadasDatosTest extends TestCase
{
    public function test_id_no_positivo_lista_vacia(): void
    {
        $repo = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $repo->expects($this->never())->method('getFasesCompletadas');
        $out = (new ActividadFasesCompletadasDatos($repo))->ejecutar(0);

        $this->assertSame(['fases_completadas' => []], $out);
    }

    public function test_mapea_ids_a_enteros(): void
    {
        $repo = $this->createStub(ActividadProcesoTareaRepositoryInterface::class);
        $repo->method('getFasesCompletadas')->with(7)->willReturn(['1', 2, '5']);

        $out = (new ActividadFasesCompletadasDatos($repo))->ejecutar(7);

        $this->assertSame(['fases_completadas' => [1, 2, 5]], $out);
    }
}
