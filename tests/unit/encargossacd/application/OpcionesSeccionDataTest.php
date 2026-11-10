<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\OpcionesSeccionData;
use src\encargossacd\application\services\EncargoAplicacionService;

final class OpcionesSeccionDataTest extends TestCase
{
    public function test_devuelve_las_opciones_del_servicio(): void
    {
        $servicio = $this->createMock(EncargoAplicacionService::class);
        $servicio->expects($this->once())->method('getArraySeccion')->willReturn(['sv' => 'sv', 'sf' => 'sf']);

        $out = (new OpcionesSeccionData($servicio))->execute();

        $this->assertSame(['opciones' => ['sv' => 'sv', 'sf' => 'sf']], $out);
    }

    public function test_sin_opciones_devuelve_lista_vacia(): void
    {
        $servicio = $this->createMock(EncargoAplicacionService::class);
        $servicio->method('getArraySeccion')->willReturn([]);

        $this->assertSame(['opciones' => []], (new OpcionesSeccionData($servicio))->execute());
    }
}
