<?php

namespace Tests\unit\actividades\application;

use src\actividades\application\ActividadTipoGetNomTipoTabla;
use Tests\myTest;

final class ActividadTipoGetNomTipoTablaTest extends myTest
{
    public function test_devuelve_html_de_tabla_no_vacio(): void
    {
        $html = (new ActividadTipoGetNomTipoTabla())->execute(['entrada' => '1']);
        $this->assertNotSame('', $html);
        $this->assertStringContainsString("class='sortable'", $html);
        $this->assertStringContainsString("id='uno'", $html);
    }
}
