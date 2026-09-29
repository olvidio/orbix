<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\PropuestasCrearTablaData;
use src\shared\security\HashB;

final class PropuestasCrearTablaDataTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-propuestas-crear-tabla');
    }

    public function test_emite_ctx_crear_tabla_accion_only(): void
    {
        $out = (new PropuestasCrearTablaData())->execute();

        $this->assertSame([], HashB::open($out['ctx_crear_tabla'], 'propuestas_ajax_crear_tabla'));
    }
}
