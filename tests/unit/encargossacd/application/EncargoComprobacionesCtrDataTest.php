<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoComprobacionesCtrData;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

final class EncargoComprobacionesCtrDataTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-comprobaciones-ctr');
    }

    public function test_emite_la_capsula_de_la_accion(): void
    {
        $out = (new EncargoComprobacionesCtrData())->execute();

        $this->assertSame([], HashB::open($out['ctx_comprobar'], EncargoComprobacionesCtrData::ACCION));
    }

    public function test_la_capsula_no_vale_para_otra_accion(): void
    {
        $out = (new EncargoComprobacionesCtrData())->execute();

        $this->expectException(HashBInvalidException::class);
        HashB::open($out['ctx_comprobar'], 'propuestas_aprobar');
    }
}
