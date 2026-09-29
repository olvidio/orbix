<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\PropuestasAprobarData;
use src\shared\security\HashB;

final class PropuestasAprobarDataTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-propuestas-aprobar');
    }

    public function test_emite_ctx_aprobar_accion_only(): void
    {
        $out = (new PropuestasAprobarData())->execute();

        $this->assertSame([], HashB::open($out['ctx_aprobar'], 'propuestas_aprobar'));
    }
}
