<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\TipoActivFormModificar;

final class TipoActivFormModificarTest extends TestCase
{
    public function test_form_contiene_campos_esperados(): void
    {
        $html = (new TipoActivFormModificar())->execute(['id_tipo_activ' => 123456]);
        $this->assertStringContainsString("frm_tipo_activ", $html);
        $this->assertStringContainsString('nom_tipo_activ', $html);
        $this->assertStringContainsString('fnjs_guardar', $html);
    }
}
