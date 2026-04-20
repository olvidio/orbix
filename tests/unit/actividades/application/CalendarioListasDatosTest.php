<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\CalendarioListasDatos;

final class CalendarioListasDatosTest extends TestCase
{
    public function test_que_desconocido_devuelve_mensaje(): void
    {
        $out = (new CalendarioListasDatos())->ejecutar(['que' => '__no_existe__']);
        $this->assertArrayHasKey('html', $out);
        $this->assertStringContainsString('opción no definida', $out['html']);
    }
}
