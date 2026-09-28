<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\TipoActivFormModificar;
use src\shared\security\HashB;

final class TipoActivFormModificarTest extends TestCase
{
    public function test_form_contiene_campos_esperados(): void
    {
        $html = (new TipoActivFormModificar())->execute(['id_tipo_activ' => 123456]);
        $this->assertStringContainsString("frm_tipo_activ", $html);
        $this->assertStringContainsString('nom_tipo_activ', $html);
        $this->assertStringContainsString('fnjs_guardar', $html);
        $this->assertStringContainsString('name="ctx_guardar"', $html);
        $this->assertStringContainsString('name="ctx_eliminar"', $html);

        if (!preg_match('/name="ctx_guardar"\s+value="([^"]+)"/', $html, $mGuardar)) {
            $this->fail('ctx_guardar no encontrado en el HTML');
        }
        if (!preg_match('/name="ctx_eliminar"\s+value="([^"]+)"/', $html, $mEliminar)) {
            $this->fail('ctx_eliminar no encontrado en el HTML');
        }
        $this->assertSame(
            ['id_tipo_activ' => 123456],
            HashB::open(html_entity_decode($mGuardar[1], ENT_QUOTES, 'UTF-8'), 'tipo_activ_update')
        );
        $this->assertSame(
            ['id_tipo_activ' => 123456],
            HashB::open(html_entity_decode($mEliminar[1], ENT_QUOTES, 'UTF-8'), 'tipo_activ_eliminar')
        );
    }
}
