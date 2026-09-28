<?php

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\TipoDocOpcionesData;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\shared\security\HashB;

final class TipoDocOpcionesDataTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-inventario-tipo-doc');
    }

    public function test_devuelve_opciones_del_repositorio(): void
    {
        $repo = $this->createMock(TipoDocRepositoryInterface::class);
        $repo->method('getArrayTipoDoc')->willReturn(['x' => 'Tipo X']);
        $service = new TipoDocOpcionesData($repo);

        $payload = $service->execute();
        $this->assertSame(['x' => 'Tipo X'], $payload['a_opciones']);
        $this->assertNotSame('', $payload['ctx_documentos_guardar']);
        $this->assertSame([], HashB::open($payload['ctx_documentos_guardar'], 'documentos_guardar'));
    }
}
