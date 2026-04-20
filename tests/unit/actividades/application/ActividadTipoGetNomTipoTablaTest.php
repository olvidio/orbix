<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadTipoGetNomTipoTabla;

final class ActividadTipoGetNomTipoTablaTest extends TestCase
{
    public function test_devuelve_html_de_tabla_no_vacio(): void
    {
        $html = (new ActividadTipoGetNomTipoTabla())->execute(['entrada' => '1']);
        $this->assertNotSame('', $html);
        $this->assertStringContainsString('grid_uno', $html);
    }
}
