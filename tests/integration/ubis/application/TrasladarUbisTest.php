<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\TrasladarUbis;
use Tests\myTest;

/**
 * Tests de integración para TrasladarUbis.
 *
 * No se pueden probar trasladoCtr/trasladoCdc "de verdad" sin alterar dos
 * delegaciones reales, así que aquí sólo se cubren:
 *  - ramas de early return (ya cubiertas en unit, se dejan también aquí como
 *    guardia para que sigan funcionando con el bootstrap completo).
 *  - ejecución sin errores con ids que no resuelven a ningún Ubi.
 */
class TrasladarUbisTest extends myTest
{
    public function test_sin_seleccion_devuelve_mensaje_de_error(): void
    {
        $msg = TrasladarUbis::execute(['dl_dst' => 'cr', 'sel' => []]);
        $this->assertSame(_('No se han seleccionado ubis.'), $msg);
    }

    public function test_sel_no_array_se_considera_vacio(): void
    {
        $msg = TrasladarUbis::execute(['dl_dst' => 'cr', 'sel' => 'no-array']);
        $this->assertSame(_('No se han seleccionado ubis.'), $msg);
    }

    public function test_seleccion_con_ids_que_no_resuelven_a_ubi_no_falla(): void
    {
        $msg = TrasladarUbis::execute([
            'dl_dst' => 'cr',
            'sel' => [99999998, 99999999],
        ]);
        $this->assertSame('', $msg, 'Debería completarse sin error aunque no existan los ubis.');
    }
}
