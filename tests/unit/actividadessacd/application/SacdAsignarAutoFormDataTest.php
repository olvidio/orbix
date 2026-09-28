<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\SacdAsignarAutoFormData;
use src\shared\security\HashB;

final class SacdAsignarAutoFormDataTest extends TestCase
{
    public function test_emite_ctx_atado_a_f_ini_iso(): void
    {
        $out = (new SacdAsignarAutoFormData())->execute();

        $this->assertMatchesRegularExpression('/^\d{4}-09-02$/', $out['f_ini_iso']);
        $this->assertSame(
            ['f_ini_iso' => $out['f_ini_iso']],
            HashB::open($out['ctx_asignar_auto'], 'sacd_asignar_auto')
        );
    }
}
