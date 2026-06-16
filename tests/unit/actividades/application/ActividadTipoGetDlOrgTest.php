<?php

namespace Tests\unit\actividades\application;

use src\actividades\application\ActividadTipoGetDlOrg;
use Tests\myTest;

final class ActividadTipoGetDlOrgTest extends myTest
{
    public function test_devuelve_id_dl_org_y_opciones(): void
    {
        $out = $GLOBALS['container']->get(ActividadTipoGetDlOrg::class)->execute(['entrada' => '1']);

        $this->assertSame('dl_org', $out['id']);
        $this->assertTrue($out['blanco']);
        $this->assertArrayHasKey('opciones', $out);
        $this->assertArrayHasKey('selected', $out);
        $this->assertNotSame('', $out['selected']);
    }
}
