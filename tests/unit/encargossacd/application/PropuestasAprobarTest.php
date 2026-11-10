<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\PropuestasAprobar;
use src\encargossacd\application\services\PropuestasAprobarService;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;

final class PropuestasAprobarTest extends TestCase
{
    public function test_devuelve_el_texto_del_servicio_y_ejecuta_la_aprobacion_una_vez(): void
    {
        $propuestas = $this->createMock(PropuestaEncargoSacdRepositoryInterface::class);
        $propuestas->expects($this->once())->method('matching')->willReturn([]);
        $limpiezas = 0;
        $servicio = new PropuestasAprobarService(
            $propuestas,
            $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class),
            $this->createMock(EncargoSacdRepositoryInterface::class),
            $this->createMock(EncargoSacdHorarioRepositoryInterface::class),
            function () use (&$limpiezas): void {
                $limpiezas++;
            }
        );

        $out = (new PropuestasAprobar($servicio))->execute();

        $this->assertSame(['text'], array_keys($out));
        $this->assertNotSame('', $out['text']);
        $this->assertSame(1, $limpiezas);
    }
}
