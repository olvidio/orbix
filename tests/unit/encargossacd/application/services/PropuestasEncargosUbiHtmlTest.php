<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application\services;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\services\PropuestasEncargosUbiHtml;
use src\encargossacd\application\services\PropuestasHorarioTexto;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;

/** Bloque HTML de las propuestas de encargos de un centro: la descripción del encargo va escapada. */
final class PropuestasEncargosUbiHtmlTest extends TestCase
{
    public function test_la_descripcion_del_encargo_va_escapada(): void
    {
        $e = new Encargo();
        $e->setId_enc(5);
        $e->setDesc_enc('<script>alert(1)</script> & "cía"');
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('matching')->willReturn([$e]);
        $propuestas = $this->createMock(PropuestaEncargoSacdRepositoryInterface::class);
        $propuestas->method('matching')->willReturn([]);
        $horario = new PropuestasHorarioTexto(
            $this->createStub(\src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface::class),
            $this->createStub(\src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface::class),
        );
        $svc = new PropuestasEncargosUbiHtml($encargos, $propuestas, $this->createStub(PersonaSacdRepositoryInterface::class), $horario);

        foreach (['editable' => $svc->editable(1010810), 'simple' => $svc->simple(1010810)] as $modo => $html) {
            $this->assertStringNotContainsString('<script>', $html, $modo);
            $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt; &amp; &quot;cía&quot;', $html, $modo);
        }
    }
}
