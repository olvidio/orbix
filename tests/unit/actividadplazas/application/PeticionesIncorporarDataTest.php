<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PeticionesIncorporarData;
use src\shared\security\HashB;

final class PeticionesIncorporarDataTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-peticiones-incorporar');
    }

    public function test_emite_ctx_incorporar_accion_only(): void
    {
        $out = (new PeticionesIncorporarData())->execute();

        $this->assertSame([], HashB::open($out['ctx_incorporar'], 'peticiones_incorporar'));
    }
}
