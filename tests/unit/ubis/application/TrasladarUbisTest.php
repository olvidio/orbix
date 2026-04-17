<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\TrasladarUbis;

final class TrasladarUbisTest extends TestCase
{
    public function test_sin_seleccion_devuelve_mensaje_error(): void
    {
        $msg = TrasladarUbis::execute(['dl_dst' => 'cr', 'sel' => []]);
        $this->assertSame(_('No se han seleccionado ubis.'), $msg);
    }

    public function test_sin_clave_sel_devuelve_mensaje_error(): void
    {
        $msg = TrasladarUbis::execute(['dl_dst' => 'cr']);
        $this->assertSame(_('No se han seleccionado ubis.'), $msg);
    }

    public function test_sel_no_array_se_trata_como_vacio(): void
    {
        $msg = TrasladarUbis::execute(['dl_dst' => 'cr', 'sel' => 'no-array']);
        $this->assertSame(_('No se han seleccionado ubis.'), $msg);
    }
}
