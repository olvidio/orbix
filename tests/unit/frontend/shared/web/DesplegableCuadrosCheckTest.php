<?php

namespace Tests\unit\frontend\shared\web;

use frontend\shared\web\Desplegable;
use Tests\myTest;

class DesplegableCuadrosCheckTest extends myTest
{
    public function test_marca_las_opciones_de_clave_numerica_cuando_lo_marcado_llega_como_string(): void
    {
        $oCuadros = new Desplegable();
        $oCuadros->setNombre('dl');
        $oCuadros->setOpciones([7 => 'dlb', 12 => 'dlp']);
        // Lo marcado llega de $_POST como string.
        $oCuadros->setChecked(['12']);

        $html = $oCuadros->cuadros_check();

        $this->assertStringContainsString('value="12" checked>dlp', $html);
        $this->assertStringContainsString('value="7" >dlb', $html);
    }

    public function test_omite_las_opciones_excluidas_aunque_lleguen_como_string(): void
    {
        $oCuadros = new Desplegable();
        $oCuadros->setNombre('dl');
        $oCuadros->setOpciones([7 => 'dlb', 12 => 'dlp']);
        $oCuadros->setOpcion_no(['7']);

        $html = $oCuadros->cuadros_check();

        $this->assertStringNotContainsString('dlb', $html);
        $this->assertStringContainsString('dlp', $html);
    }
}
