<?php

namespace Tests\integration\procesos\application;

use src\procesos\application\TipoActivProcesoLstPosibles;
use Tests\myTest;

/**
 * Test de integración smoke para TipoActivProcesoLstPosibles: debe
 * devolver un HTML con la cabecera "procesos" aunque no haya ninguno.
 */
class TipoActivProcesoLstPosiblesTest extends myTest
{
    public function test_devuelve_tabla_html(): void
    {
        $html = (new TipoActivProcesoLstPosibles())->execute([
            'id_tipo_activ' => 0,
            'propio' => 't',
        ]);

        $this->assertIsString($html);
        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('</table>', $html);
        $this->assertStringContainsString(_('procesos'), $html);
    }
}
