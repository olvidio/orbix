<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadTipoGetTarifas;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;

final class ActividadTipoGetTarifasTest extends TestCase
{
    public function test_desplegable_de_la_seccion_sin_seleccion(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayTipoTarifas')
            ->with(2)
            ->willReturn([9 => 'E', 1 => 'I']);

        $out = (new ActividadTipoGetTarifas($repo))->execute(['entrada' => '2']);

        $this->assertSame('id_tarifa', $out['id']);
        $this->assertSame('', $out['selected']);
        $this->assertTrue($out['blanco']);
        $this->assertSame([['9', 'E'], ['1', 'I']], $out['opciones']);
    }
}
