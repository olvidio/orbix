<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\ListasComTxtGet;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTexto;

final class ListasComTxtGetTest extends TestCase
{
    public function test_sin_filas_devuelve_texto_vacio(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getEncargoTextos')
            ->with(['clave' => 'c1', 'idioma' => 'es_ES.UTF-8'])
            ->willReturn([]);

        $useCase = new ListasComTxtGet($repo);
        $this->assertSame(['texto' => ''], $useCase->execute('c1', 'es_ES.UTF-8'));
    }

    public function test_getEncargoTextos_false_trata_como_vacio(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([]);

        $useCase = new ListasComTxtGet($repo);
        $this->assertSame(['texto' => ''], $useCase->execute('k', 'ca_ES.UTF-8'));
    }

    public function test_primera_fila(): void
    {
        $row = $this->createMock(EncargoTexto::class);
        $row->method('getTexto')->willReturn('Hola');

        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([$row]);

        $useCase = new ListasComTxtGet($repo);
        $this->assertSame(['texto' => 'Hola'], $useCase->execute('x', 'es_ES.UTF-8'));
    }
}
