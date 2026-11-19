<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain;

use PHPUnit\Framework\TestCase;
use src\menus\domain\PermisoMenuBits;
use src\shared\domain\PermisoRevisarAyuda;

final class PermisoRevisarAyudaTest extends TestCase
{
    public function test_un_administrador_sv_o_sf_puede_revisar_las_consultas(): void
    {
        $bits = PermisoMenuBits::map();

        $this->assertTrue(PermisoRevisarAyuda::puede($bits['admin_sv']));
        $this->assertTrue(PermisoRevisarAyuda::puede($bits['admin_sf']));
        $this->assertTrue(PermisoRevisarAyuda::puede($bits['admin_sv'] | $bits['est']));
    }

    public function test_quien_no_es_administrador_no_puede(): void
    {
        $bits = PermisoMenuBits::map();

        $this->assertFalse(PermisoRevisarAyuda::puede(0));
        $this->assertFalse(PermisoRevisarAyuda::puede($bits['est'] | $bits['sm'] | $bits['ctr']));
    }
}
