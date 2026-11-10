<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PlazasCalendarioMensaje;

final class PlazasCalendarioMensajeTest extends TestCase
{
    public function test_el_mensaje_explica_que_hacer_cuando_falta_el_registro_de_calendario(): void
    {
        $m = PlazasCalendarioMensaje::faltaRegistro();

        $this->assertNotSame('', $m);
        $this->assertStringContainsString('calendario', $m);
        $this->assertSame(4, preg_match_all('/^\d\. /m', $m), 'los cuatro pasos numerados');
    }
}
